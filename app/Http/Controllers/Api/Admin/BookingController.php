<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use App\Models\BookingNotification;
use App\Models\BookingPermanentbooking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Get upcoming bookings for the venue.
     */
    public function upcoming(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;

        $bookings = BookingBooking::where('complex_id_id', $venueId)
            ->where('booking_date', '>=', now()->format('Y-m-d'))
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate($request->per_page ?? 20);

        return response()->json($bookings);
    }

    /**
     * Get today's bookings.
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;

        $bookings = BookingBooking::where('complex_id_id', $venueId)
            ->where('booking_date', now()->format('Y-m-d'))
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json(['results' => $bookings]);
    }

    /**
     * Get slot availability for a specific sport and date.
     */
    public function getSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'required|integer|exists:booking_sport,id',
            'date'     => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sport = BookingSport::find($request->sport_id);
        $venue = BookingVenue::find($sport->venue_id);
        $maxCourts = (int)($sport->maximum_court ?? 1);
        $date = $request->date;
        $now = Carbon::now();
        $isToday = $date === $now->format('Y-m-d');

        // Determine opening hours based on the day of the week (prefer sport-specific, fallback to venue)
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        $openingHours = null;
        if (!empty($sport->opening_hours)) {
            $openingHours = is_array($sport->opening_hours) ? $sport->opening_hours : json_decode($sport->opening_hours, true);
        }
        
        $dayConfig = null;
        if ($openingHours && isset($openingHours[$dayOfWeek])) {
            $dayConfig = $openingHours[$dayOfWeek];
        }
        
        if (!$dayConfig || (empty($dayConfig['open']) && empty($dayConfig['close']) && !isset($dayConfig['closed']))) {
            $venueHours = is_array($venue->opening_hours) ? $venue->opening_hours : json_decode($venue->opening_hours, true) ?? [];
            $dayConfig = $venueHours[$dayOfWeek] ?? null;
        }

        // If the venue is explicitly closed, return no slots and a specific message
        if ($dayConfig && isset($dayConfig['closed']) && $dayConfig['closed'] == true) {
            return response()->json([
                'sport_id'   => (int)$request->sport_id,
                'sport_name' => $sport->name,
                'date'       => $date,
                'courts'     => [],
                'message'    => 'Venue is closed on this day.',
            ]);
        }

        $openTime = $dayConfig['open'] ?? '06:00';
        $closeTime = $dayConfig['close'] ?? '23:00';
        
        $startHour = (int) explode(':', $openTime)[0];
        $endHour = (int) explode(':', $closeTime)[0];
        
        if ($endHour <= $startHour && $endHour !== 0) {
            $endHour = 24; 
        } elseif ($endHour === 0) {
            $endHour = 24;
        }

        $timeSlots = [];
        for ($h = $startHour; $h < $endHour; $h++) {
            $timeSlots[] = sprintf("%02d:00:00", $h);
        }

        $existingBookings = BookingBooking::where('game_id_id', $request->sport_id)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
            ->get();

        // 2. Parse blocked slots
        $blocked = $sport->blocked_slots;
        if (is_string($blocked)) {
            $blocked = json_decode($blocked, true) ?? [];
        }
        if (!is_array($blocked)) {
            $blocked = [];
        }

        $courts = [];
        for ($c = 1; $c <= $maxCourts; $c++) {
            $slots = [];
            foreach ($timeSlots as $time) {
                $hour = (int)substr($time, 0, 2);
                $isPast = $isToday && $hour < $now->hour;
                
                // 1. Check existing bookings
                $bookingModel = $existingBookings->where('start_time', $time)->where('court_number', (string)$c)->first();
                
                // 2. Check blocked slots
                $isBlocked = (isset($blocked[$dayOfWeek]) && is_array($blocked[$dayOfWeek]) && in_array($time, $blocked[$dayOfWeek], true)) || isset($blocked[$date][$time][(string)$c]);

                $status = 'available';
                if ($bookingModel) {
                    $status = 'booked';
                } elseif ($isBlocked) {
                    $status = 'blocked';
                } elseif ($isPast) {
                    $status = 'past';
                }

                $period = $hour >= 12 ? 'PM' : 'AM';
                $h12 = $hour % 12 ?: 12;
                $display = sprintf("%02d:00 %s", $h12, $period);

                $bookingData = null;
                if ($bookingModel) {
                    $permanentWeeks = null;
                    if ($bookingModel->permanent_source_id) {
                        $parent = BookingPermanentbooking::find($bookingModel->permanent_source_id);
                        if ($parent && isset($parent->recurring_config['weeks'])) {
                            $permanentWeeks = $parent->recurring_config['weeks'];
                        }
                    }

                    if (empty($bookingModel->qr_code)) {
                        $bookingModel->qr_code = 'QR' . strtoupper(substr(md5(uniqid($bookingModel->id, true)), 0, 6));
                        $bookingModel->save();
                    }

                    $bookingData = [
                        'id' => $bookingModel->id,
                        'customer_name' => $bookingModel->user_name,
                        'customer_phone' => $bookingModel->user_number,
                        'status' => $bookingModel->status,
                        'payment_status' => $bookingModel->payment_status,
                        'is_permanent' => (bool)$bookingModel->permanent_source_id,
                        'permanent_source_id' => $bookingModel->permanent_source_id,
                        'permanent_weeks' => $permanentWeeks,
                        'price' => (string)$bookingModel->price,
                        'is_admin_booking' => true,
                        'notes' => $bookingModel->notes ?? '',
                        'booking_date' => $bookingModel->booking_date->format('Y-m-d'),
                        'start_time' => $bookingModel->start_time,
                        'end_time' => $bookingModel->end_time,
                        'qr_code' => $bookingModel->qr_code,
                    ];
                }

                $slots[] = [
                    'start_time'   => $time,
                    'end_time'     => sprintf("%02d:00:00", $hour + 1),
                    'display_time' => $display,
                    'status'       => $status,
                    'is_past'      => $isPast,
                    'booking'      => $bookingData,
                ];
            }
            $courts[] = [
                'court_number' => $c,
                'court_name'   => "Court $c",
                'slots'        => $slots,
            ];
        }

        return response()->json([
            'sport_id'   => (int)$request->sport_id,
            'sport_name' => $sport->name,
            'date'       => $date,
            'courts'     => $courts,
        ]);
    }

    /**
     * Create a manual booking (potentially permanent/recurring).
     */
    public function create(Request $request)
    {
        // Align with frontend payload: customer_name, customer_phone, slots array
        $data = $request->all();
        
        // Map frontend fields to controller expectations
        if ($request->has('customer_name')) $data['user_name'] = $request->customer_name;
        if ($request->has('customer_phone')) $data['user_number'] = $request->customer_phone;
        
        if ($request->has('slots') && is_array($request->slots) && count($request->slots) > 0) {
            $slot = $request->slots[0];
            $data['start_time'] = $slot['start_time'] ?? null;
            $data['end_time'] = $slot['end_time'] ?? null;
            $data['price'] = $slot['price'] ?? null;
            $data['duration'] = $slot['duration'] ?? 60;
        }

        if (empty($data['venue_id'])) {
            $sport = BookingSport::find($data['sport_id'] ?? null);
            if ($sport) {
                $data['venue_id'] = $sport->venue_id;
            } else {
                $data['venue_id'] = $request->user()->complex_id;
            }
        }

        $validator = Validator::make($data, [
            'sport_id'       => 'required|integer|exists:booking_sport,id',
            'venue_id'       => 'required|integer|exists:booking_venue,id',
            'booking_date'   => 'required|date',
            'start_time'     => 'required|string',
            'end_time'       => 'required|string',
            'court_number'   => 'required|string',
            'user_name'      => 'required|string|max:255',
            'user_number'    => 'nullable|string|max:20',
            'price'          => 'required|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'notes'          => 'nullable|string',
            'is_permanent'   => 'nullable',
            'permanent_weeks'=> 'required_if:is_permanent,true,1|nullable|integer|min:2|max:52',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sport = BookingSport::find($data['sport_id']);
        $startTime = $this->normalizeTime($data['start_time']);
        $endTime = $this->normalizeTime($data['end_time']);
        $date = $data['booking_date'];
        $court = $data['court_number'];

        $bookings = [];
        $isPermanent = $request->boolean('is_permanent');
        $weeks = $isPermanent ? (int)($data['permanent_weeks'] ?? 1) : 1;
        $currentDate = Carbon::parse($date);

        DB::beginTransaction();
        DB::connection()->enableQueryLog();
        try {
            $permanentSourceId = null;

            // 1. If permanent, create the parent record first to satisfy Foreign Key constraint
            if ($isPermanent) {
                $permanentParent = BookingPermanentbooking::create([
                    'user_id'          => $request->user()->id,
                    'sport_id'         => $data['sport_id'],
                    'complex_id'       => $data['venue_id'],
                    'start_time'       => $startTime,
                    'end_time'         => $endTime,
                    'duration'         => $data['duration'] ?? 60,
                    'price'            => $data['price'],
                    'created_at'       => now(),
                    'recurring_config' => [
                        'weeks' => $weeks,
                        'start_date' => $date,
                        'court' => $court
                    ],
                ]);
                $permanentSourceId = $permanentParent->id;
            }

            for ($i = 0; $i < $weeks; $i++) {
                $targetDate = $currentDate->format('Y-m-d');

                // Check availability - MORE ROBUST CHECK
                $conflict = BookingBooking::where('game_id_id', $data['sport_id'])
                    ->where('complex_id_id', $data['venue_id'])
                    ->where('booking_date', $targetDate)
                    ->where('start_time', $startTime)
                    ->where('court_number', $court)
                    ->whereNotIn('status', ['cancelled', 'Cancelled'])
                    ->first();

                if ($conflict) {
                    throw new \Exception("Slot already booked on $targetDate at $startTime on Court $court (Booking Status: {$conflict->status})");
                }

                // Check blocked slots
                $blocked = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
                if (isset($blocked[$targetDate][$startTime][$court])) {
                    $reason = $blocked[$targetDate][$startTime][$court]['reason'] ?? 'Blocked for maintenance';
                    throw new \Exception("Slot is blocked on $targetDate at $startTime on Court $court. Reason: $reason");
                }

                // Check recurring (day-of-week) blocked slots
                $dayKey    = strtolower(Carbon::parse($targetDate)->format('l'));
                $recurring = $blocked[$dayKey] ?? [];
                if (is_array($recurring) && in_array($startTime, $recurring, true)) {
                    throw new \Exception("Slot is recurring-blocked every $dayKey at $startTime for {$sport->name}.");
                }

                $booking = BookingBooking::create([
                    'user_id_id'           => $request->user()->id,
                    'game_id_id'           => $data['sport_id'],
                    'game_name'            => $sport->name,
                    'complex_id_id'        => $data['venue_id'],
                    'booking_date'         => $targetDate,
                    'date'                 => $targetDate,
                    'start_time'           => $startTime,
                    'end_time'             => $endTime,
                    'time_slot'            => Carbon::parse($startTime)->format('h:i A'),
                    'court_number'         => $court,
                    'user_name'            => $data['user_name'],
                    'user_number'          => $data['user_number'] ?? '',
                    'duration'             => $data['duration'] ?? 60,
                    'price'                => $data['price'],
                    'payment_method'       => $data['payment_method'] ?? 'cash',
                    'payment_status'       => $data['payment_status'] ?? 'Pending',
                    'status'               => 'confirmed',
                    'notes'                => $data['notes'] ?? null,
                    'is_challenge_booking' => false,
                    'permanent_source_id'  => $permanentSourceId,
                    'qr_code'              => 'QR' . strtoupper(substr(md5(uniqid('', true)), 0, 6)),
                ]);

                // BookingBookingObserver broadcasts BookingCreated automatically.

                $bookings[] = $booking;
                $currentDate->addWeek();
            }

            DB::commit();

            // Notify staff with latest booking details for app notifications
            $latest = $bookings[0] ?? null;
            $this->notifyStaff(
                $data['venue_id'],
                'new_booking',
                'New Booking',
                "New booking created for {$data['user_name']}",
                [
                    'booking_id' => $latest?->id,
                    'game_name' => $sport->name,
                    'court_number' => $court,
                    'booking_date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'user_name' => $data['user_name'],
                    'price' => (string)$data['price'],
                    'is_permanent' => $isPermanent,
                    'permanent_weeks' => $isPermanent ? $weeks : null,
                ]
            );

            return response()->json([
                'message' => $isPermanent ? 'Permanent bookings created successfully' : 'Booking created successfully',
                'bookings' => $bookings,
                'is_permanent' => $isPermanent,
                'permanent_source_id' => $permanentSourceId,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            $queries = DB::getQueryLog();
            Log::error("Booking creation failed: " . $e->getMessage(), ['queries' => $queries, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to create booking: ' . $e->getMessage(),
                'detail'  => $e->getMessage(),
                'queries' => $queries
            ], 500);
        }
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $booking = BookingBooking::where('complex_id_id', $user->complex_id)->find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $validator = Validator::make($request->all(), ['status' => 'required|string']);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $oldStatus = $booking->status;
        $newStatus = (string)$request->status;
        $booking->update(['status' => $newStatus]);

        $normalized = strtolower(str_replace(['_', '-'], ' ', $newStatus));
        $type = 'booking_updated';
        if ($normalized === 'cancelled') $type = 'booking_cancelled';
        if ($normalized === 'completed') $type = 'booking_completed';
        if ($normalized === 'confirmed') $type = 'booking_confirmed';

        $this->notifyStaff(
            $user->complex_id,
            $type,
            'Booking Status Updated',
            "Booking #{$booking->id} status changed from {$oldStatus} to {$newStatus}",
            [
                'booking_id' => $booking->id,
                'game_name' => $booking->game_name,
                'court_number' => $booking->court_number,
                'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string)$booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user_name,
                'price' => (string)$booking->price,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );

        // Load relationships and return mapped response matching frontend Booking type
        $booking->load(['sport', 'venue']);

        // Calculate time completion status
        $now = Carbon::now('Asia/Colombo');
        $isTimeCompleted = false;
        $displayStatus = $booking->status;

        try {
            $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
            $endAt = Carbon::parse("{$bookingDate} {$booking->end_time}", 'Asia/Colombo');
            $startAt = Carbon::parse("{$bookingDate} {$booking->start_time}", 'Asia/Colombo');
            if ($endAt->lte($startAt)) {
                $endAt->addDay();
            }
            $isTimeCompleted = $now->gte($endAt);

            if ($isTimeCompleted) {
                $status = strtolower($booking->status);
                if ($status === 'playing') $displayStatus = 'Played';
                elseif ($status === 'no-show') $displayStatus = 'No-Show';
                elseif ($status === 'cancelled') $displayStatus = 'Cancelled';
                elseif ($status === 'confirmed' || $status === 'upcoming') $displayStatus = 'Not Played';
            }
        } catch (\Exception $e) {
            Log::error("Error calculating time completion in updateStatus: " . $e->getMessage());
        }

        return response()->json([
            'id'                  => $booking->id,
            'venue_name'          => $booking->venue->name ?? '',
            'sport_name'          => $booking->sport->name ?? $booking->game_name ?? '',
            'court_number'        => $booking->court_number,
            'booking_date'        => Carbon::parse($booking->booking_date)->format('Y-m-d'),
            'start_time'          => $booking->start_time,
            'end_time'            => $booking->end_time,
            'time_slot'           => $booking->time_slot,
            'price'               => (string) $booking->price,
            'status'              => $booking->status,
            'user_email'          => $booking->user->email ?? null,
            'user_phone'          => $booking->user_number ?? null,
            'user_name'           => $booking->user_name ?? '',
            'is_permanent'        => (bool) $booking->permanent_source_id,
            'is_challenge_booking'=> (bool) $booking->is_challenge_booking,
            'notes'               => $booking->notes,
            'payment_status'      => $booking->payment_status,
            'qr_code'             => $booking->qr_code,
            'is_time_completed'   => $isTimeCompleted,
            'display_status'      => $displayStatus,
            'created_at'          => $booking->created_at->toDateTimeString(),
            'updated_at'          => $booking->updated_at->toDateTimeString(),
        ]);
    }

    /**
     * Update booking payment status.
     */
    public function updatePayment(Request $request, $id)
    {
        $user = $request->user();
        $booking = BookingBooking::where('complex_id_id', $user->complex_id)->find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $validator = Validator::make($request->all(), [
            'payment_status' => 'sometimes|required|string',
            'price' => 'sometimes|numeric|min:0',
        ]);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $updateData = [];

        $oldPaymentStatus = $booking->payment_status;
        $oldPrice = (string)$booking->price;

        if ($request->has('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
        }

        if ($request->has('price')) {
            $updateData['price'] = $request->price;
        }

        if (empty($updateData)) {
            return response()->json(['message' => 'Nothing to update'], 422);
        }

        $booking->update($updateData);

        $this->notifyStaff(
            $user->complex_id,
            'booking_updated',
            'Booking Payment Updated',
            "Payment details updated for booking #{$booking->id}",
            [
                'booking_id' => $booking->id,
                'game_name' => $booking->game_name,
                'court_number' => $booking->court_number,
                'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string)$booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user_name,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $booking->payment_status,
                'old_price' => $oldPrice,
                'new_price' => (string)$booking->price,
                'price' => (string)$booking->price,
            ]
        );

        return response()->json($booking);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $booking = BookingBooking::where('complex_id_id', $user->complex_id)->find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $refundRequested = filter_var($request->input('refund', false), FILTER_VALIDATE_BOOL);
        $updateData = ['status' => 'cancelled'];
        if ($refundRequested) {
            $updateData['payment_status'] = 'refunded';
        }

        $booking->update($updateData);

        $this->notifyStaff(
            $user->complex_id,
            'booking_cancelled',
            'Booking Cancelled',
            $refundRequested
                ? "Booking #{$booking->id} has been cancelled and refunded"
                : "Booking #{$booking->id} has been cancelled",
            [
                'booking_id' => $booking->id,
                'game_name' => $booking->game_name,
                'court_number' => $booking->court_number,
                'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string)$booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user_name,
                'price' => (string)$booking->price,
                'refund' => $refundRequested,
                'payment_status' => $refundRequested ? 'refunded' : (string) $booking->payment_status,
            ]
        );

        return response()->json(['message' => 'Booking cancelled', 'booking' => $booking]);
    }

    /**
     * List permanent (recurring) bookings.
     */
    public function permanentList(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;

        // Group by permanent_source_id to show recurring series
        $groups = BookingBooking::where('complex_id_id', $venueId)
            ->whereNotNull('permanent_source_id')
            ->select('permanent_source_id', DB::raw('count(*) as active_bookings_count'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('permanent_source_id')
            ->get();

        $results = [];
        foreach ($groups as $group) {
            $first = BookingBooking::find($group->permanent_source_id);
            if (!$first) continue;

            $allInSeries = BookingBooking::where('permanent_source_id', $group->permanent_source_id)
                ->orderBy('booking_date', 'asc')
                ->get();

            $results[] = [
                'id' => $first->id,
                'sport_id' => $first->game_id_id,
                'sport_name' => $first->game_name,
                'customer_name' => $first->user_name,
                'customer_phone' => $first->user_number,
                'start_time' => $first->start_time,
                'end_time' => $first->end_time,
                'price' => (string)$first->price,
                'created_at' => $first->created_at->toDateTimeString(),
                'active_bookings_count' => $group->active_bookings_count,
                'cancelled_count' => $allInSeries->where('status', 'cancelled')->count(),
                'bookings' => $allInSeries->map(function($b) {
                    return [
                        'id' => $b->id,
                        'booking_date' => $b->booking_date->format('Y-m-d'),
                        'start_time' => $b->start_time,
                        'end_time' => $b->end_time,
                        'court_number' => $b->court_number,
                        'status' => $b->status,
                        'price' => (string)$b->price,
                        'customer_name' => $b->user_name,
                    ];
                }),
            ];
        }

        return response()->json([
            'count' => count($results),
            'results' => $results
        ]);
    }

    /**
     * Cancel all bookings in a permanent series.
     */
    public function permanentCancelAll(Request $request, $id)
    {
        $user = $request->user();
        $venueId = $user->complex_id;
        $refundRequested = filter_var($request->input('refund', false), FILTER_VALIDATE_BOOL);

        $updateData = ['status' => 'cancelled'];
        if ($refundRequested) {
            $updateData['payment_status'] = 'refunded';
        }

        $count = BookingBooking::where('complex_id_id', $venueId)
            ->where('permanent_source_id', $id)
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
            ->update($updateData);

        return response()->json([
            'message' => $refundRequested
                ? "Successfully cancelled and refunded $count bookings in the series."
                : "Successfully cancelled $count bookings in the series.",
            'cancelled_count' => $count
        ]);
    }

    /**
     * Normalize time format to HH:MM:SS
     */
    private function normalizeTime($t)
    {
        if (!$t) return null;
        $t = trim($t);
        if (stripos($t, 'AM') !== false || stripos($t, 'PM') !== false) {
            return date('H:i:s', strtotime($t));
        }
        $parts = explode(':', $t);
        $h = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $m = str_pad($parts[1] ?? '00', 2, '0', STR_PAD_LEFT);
        $s = str_pad($parts[2] ?? '00', 2, '0', STR_PAD_LEFT);
        return "$h:$m:$s";
    }

    /**
     * Scan and verify booking by QR code
     */
    public function scanBooking(Request $request, $qrCode)
    {
        $user = $request->user();
        $venueId = $user->complex_id;

        $booking = BookingBooking::where('complex_id_id', $venueId)
            ->where('qr_code', $qrCode)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found for this QR code.'], 404);
        }

        // Load relationships for proper names
        $booking->load(['sport', 'venue']);

        // Calculate whether the booking time slot has ended
        $now = Carbon::now('Asia/Colombo');
        $isTimeCompleted = false;
        $displayStatus = $booking->status;

        try {
            $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
            $endTime = $booking->end_time;
            $startTime = $booking->start_time;

            $endAt = Carbon::parse("{$bookingDate} {$endTime}", 'Asia/Colombo');
            $startAt = Carbon::parse("{$bookingDate} {$startTime}", 'Asia/Colombo');

            // Handle overnight slots (end time is next day)
            if ($endAt->lte($startAt)) {
                $endAt->addDay();
            }

            $isTimeCompleted = $now->gte($endAt);

            if ($isTimeCompleted) {
                $status = strtolower($booking->status);
                if ($status === 'playing') {
                    $displayStatus = 'Played';
                } elseif ($status === 'no-show') {
                    $displayStatus = 'No-Show';
                } elseif ($status === 'cancelled') {
                    $displayStatus = 'Cancelled';
                } elseif ($status === 'confirmed' || $status === 'upcoming') {
                    $displayStatus = 'Not Played';
                } else {
                    $displayStatus = $booking->status;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error calculating booking time completion: " . $e->getMessage());
        }

        // Build response with field names matching the frontend Booking type
        $data = [
            'id'                  => $booking->id,
            'venue_name'          => $booking->venue->name ?? '',
            'sport_name'          => $booking->sport->name ?? $booking->game_name ?? '',
            'court_number'        => $booking->court_number,
            'booking_date'        => Carbon::parse($booking->booking_date)->format('Y-m-d'),
            'start_time'          => $booking->start_time,
            'end_time'            => $booking->end_time,
            'time_slot'           => $booking->time_slot,
            'price'               => (string) $booking->price,
            'status'              => $booking->status,
            'user_email'          => $booking->user->email ?? null,
            'user_phone'          => $booking->user_number ?? null,
            'user_name'           => $booking->user_name ?? '',
            'is_permanent'        => (bool) $booking->permanent_source_id,
            'is_challenge_booking'=> (bool) $booking->is_challenge_booking,
            'notes'               => $booking->notes,
            'payment_status'      => $booking->payment_status,
            'qr_code'             => $booking->qr_code,
            'is_time_completed'   => $isTimeCompleted,
            'display_status'      => $displayStatus,
            'created_at'          => $booking->created_at->toDateTimeString(),
            'updated_at'          => $booking->updated_at->toDateTimeString(),
        ];

        return response()->json($data);
    }

    /**
     * Notify staff members of a venue.
     */
    private function notifyStaff($venueId, string $type, string $title, string $message, array $data = [])
    {
        try {
            $staffIds = User::where('complex_id', $venueId)
                ->whereIn('role', ['staff', 'facility_owner', 'indoor_admin'])
                ->pluck('id');

            foreach ($staffIds as $staffId) {
                BookingNotification::create([
                    'user_id' => $staffId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to notify staff: " . $e->getMessage());
        }
    }
}
