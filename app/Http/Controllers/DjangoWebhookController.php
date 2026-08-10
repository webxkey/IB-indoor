<?php

namespace App\Http\Controllers;

use App\Events\BookingCreated;
use App\Jobs\ProcessDjangoWebhookEvent;
use App\Models\BookingBooking;
use App\Models\DjangoWebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DjangoWebhookController extends Controller
{
    private const ALLOWED_EVENT_TYPES = [
        'slot.hold.created',
        'slot.hold.released',
        'booking.created',
        'booking.cancelled',
        'slot.blocked',
        'slot.unblocked',
    ];

    private const TIMESTAMP_TOLERANCE_SECONDS = 300;

    public function receive(Request $request): JsonResponse
    {
        // ── 1. Verify source header ───────────────────────────────────────────
        if ($request->header('X-Webhook-Source') !== 'django') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ── 2. Verify timestamp freshness ─────────────────────────────────────
        $timestamp = $request->header('X-Webhook-Timestamp');
        if (!$timestamp || !ctype_digit((string) $timestamp)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $drift = abs(time() - (int) $timestamp);
        if ($drift > self::TIMESTAMP_TOLERANCE_SECONDS) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ── 3. Verify HMAC-SHA256 signature ───────────────────────────────────
        $signatureHeader = $request->header('X-Webhook-Signature', '');
        if (!str_starts_with($signatureHeader, 'sha256=')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $secret   = config('services.django_webhook.secret');
        $rawBody  = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);

        if (!hash_equals($expected, $signatureHeader)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ── 4. Parse body ─────────────────────────────────────────────────────
        $body = json_decode($rawBody, true);
        if (!is_array($body)) {
            return response()->json(['error' => 'Invalid JSON'], 422);
        }

        // ── 5. Validate envelope schema ───────────────────────────────────────
        $v = Validator::make($body, [
            'schema_version' => 'required|string',
            'event_id'       => 'required|uuid',
            'event_type'     => ['required', 'string', 'in:' . implode(',', self::ALLOWED_EVENT_TYPES)],
            'occurred_at'    => 'required|string',
            'source_system'  => 'required|in:django',
            'payload'        => 'required|array',
            'payload.sport_id'   => 'required|integer',
            'payload.venue_id'   => 'required|integer',
            'payload.date'       => 'required|date',
            'payload.start_time' => 'required|string',
            'payload.end_time'   => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $v->errors()], 422);
        }

        $eventId   = $body['event_id'];
        $eventType = $body['event_type'];

        // ── 6. Idempotency check ──────────────────────────────────────────────
        $existing = DjangoWebhookEvent::where('event_id', $eventId)->first();
        if ($existing) {
            return response()->json(['status' => 'duplicate', 'event_id' => $eventId]);
        }

        // ── 7. Persist and queue ──────────────────────────────────────────────
        try {
            $record = DjangoWebhookEvent::create([
                'event_id'     => $eventId,
                'event_type'   => $eventType,
                'received_at'  => now(),
                'status'       => 'received',
                'payload_json' => $body,
            ]);

            ProcessDjangoWebhookEvent::dispatch($record->id);
        } catch (\Throwable $e) {
            Log::error('DjangoWebhook: failed to persist event', [
                'event_id' => $eventId,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }

        return response()->json(['status' => 'accepted', 'event_id' => $eventId]);
    }

    /**
     * Simple notify endpoint for Django: one URL, one secret, one field.
     *
     *   POST /api/integration/booking-notify
     *   Header: X-Webhook-Secret: <DJANGO_LARAVEL_WEBHOOK_SECRET>
     *   Body:   { "booking_id": 123 }
     *
     * Laravel loads the booking from the shared DB and broadcasts BookingCreated,
     * which the staff dashboard listens for on bookings.complex.{venueId}.
     */
    public function notifyBooking(Request $request): JsonResponse
    {
        $secret = config('services.django_webhook.secret');
        if (!$secret || !hash_equals($secret, (string) $request->header('X-Webhook-Secret', ''))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bookingId = (int) $request->input('booking_id');
        if ($bookingId <= 0) {
            return response()->json(['error' => 'booking_id is required'], 422);
        }

        $booking = BookingBooking::find($bookingId);
        $venueId = null;

        if ($booking) {
            $venueId = $booking->complex_id_id;
        } else {
            $poolBooking = \App\Models\PoolsPoolbooking::with('pool')->find($bookingId);
            if ($poolBooking) {
                $booking = $poolBooking;
                $venueId = $poolBooking->pool ? $poolBooking->pool->venue_id : null;
            }
        }

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Broadcasting failure (e.g. Reverb not running on this host) must not
        // turn this notify endpoint into a 500. The booking already exists in
        // the DB; the live update is best-effort.
        $broadcastStatus = 'broadcasted';
        try {
            broadcast(new BookingCreated($booking));
        } catch (\Throwable $e) {
            $broadcastStatus = 'queued_locally';
            Log::warning('booking-notify: broadcast failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status'     => $broadcastStatus,
            'booking_id' => $booking->id,
            'venue_id'   => $venueId,
        ]);
    }
}
