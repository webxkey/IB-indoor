<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\BookingBooking;

class PollController extends Controller
{
    /**
     * Show the polling view.
     */
    public function view(Request $request)
    {
        // If the request expects JSON (ajax/polling), route it to the poll() method.
        if ($request->wantsJson() || $request->ajax() || $request->query('poll')) {
            return $this->poll($request);
        }

        // Otherwise return the polling view for human users.
        return view('poll');
    }

    /**
     * Simple polling endpoint to return booking updates for the authenticated user's complex.
     * Query params supported:
     * - since : ISO-8601 timestamp (or any format Carbon can parse). Returns bookings updated after this time.
     * - last_id : integer - returns bookings with id > last_id
     *
     * Response JSON:
     * {
     *   status: 'ok'|'error',
     *   changed: bool,
     *   count: int,
     *   latest: timestamp|null,
     *   bookings: [ ... ]
     * }
     */
    public function poll(Request $request): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
            }

            $user = Auth::user();
            $complexId = $user->complex_id;

            // Support both 'since' (timestamp) and 'last_id'
            $since = $request->query('since');
            $lastId = $request->query('last_id');

            $query = BookingBooking::query();
            if ($complexId) {
                $query->where('complex_id_id', $complexId);
            }

            if ($since) {
                try {
                    $dt = Carbon::parse($since);
                    $query->where('updated_at', '>', $dt->toDateTimeString());
                } catch (\Exception $e) {
                    // If parse fails, ignore and fall back to updated_at > 0 (no-op)
                    Log::warning('PollController: invalid since param', ['since' => $since]);
                }
            } elseif ($lastId) {
                $query->where('id', '>', (int) $lastId);
            } else {
                // If no filters provided, do a lightweight check for any updates in the last 10 seconds
                $query->where('updated_at', '>=', Carbon::now()->subSeconds(10)->toDateTimeString());
            }

            $bookings = $query->orderBy('updated_at', 'desc')->limit(50)->get();

            $changed = $bookings->isNotEmpty();
            $count = $bookings->count();
            $latest = $bookings->first() ? $bookings->first()->updated_at->toDateTimeString() : null;

            // Map bookings to a lightweight payload the frontend can use
            $payload = $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'game_name' => $b->game_name,
                    'booking_date' => $b->booking_date,
                    'court_number' => $b->court_number,
                    'start_time' => $b->start_time,
                    'end_time' => $b->end_time,
                    'status' => $b->status,
                    'user_name' => $b->user_name,
                    'user_number' => $b->user_number,
                    'updated_at' => $b->updated_at ? $b->updated_at->toDateTimeString() : null,
                ];
            })->toArray();

            return response()->json([
                'status' => 'ok',
                'changed' => $changed,
                'count' => $count,
                'latest' => $latest,
                'bookings' => $payload,
            ], 200);
        } catch (\Exception $e) {
            Log::error('PollController::poll failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    /**
     * Return a simple JSON status for polling.
     */
    
}
