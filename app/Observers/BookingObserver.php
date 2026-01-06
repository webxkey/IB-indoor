<?php

namespace App\Observers;

use App\Models\BookingBooking;
use App\Http\Controllers\BookingStreamController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * BookingObserver - Handles real-time booking synchronization
 * 
 * Uses Server-Sent Events (SSE) to push booking changes to all connected clients.
 * No WebSockets or polling required - updates only occur when data changes.
 */
class BookingObserver
{
    /**
     * Handle the BookingBooking "created" event.
     */
    public function created(BookingBooking $booking): void
    {
        try {
            $complexId = $booking->complex_id_id;
            
            // Queue SSE event for streaming to clients
            BookingStreamController::queueEvent($complexId, 'booking.created', [
                'id' => $booking->id,
                'booking_id' => $booking->id,
                'game_name' => $booking->game_name,
                'booking_date' => $booking->booking_date,
                'court_number' => $booking->court_number,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user_name,
                'user_number' => $booking->user_number,
                'status' => $booking->status,
                'complex_id' => $complexId,
            ]);

            // Increment booking change counter for Livewire reactive updates
            $this->incrementChangeCounter($complexId);

            Log::info('Booking created - SSE event queued', [
                'booking_id' => $booking->id,
                'complex_id' => $complexId,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to queue booking created event', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the BookingBooking "updated" event.
     */
    public function updated(BookingBooking $booking): void
    {
        try {
            // Only broadcast for important field changes
            if (!$booking->wasChanged(['status', 'start_time', 'end_time', 'court_number', 'booking_date'])) {
                return;
            }

            $complexId = $booking->complex_id_id;
            $eventType = $booking->status === 'Cancelled' ? 'booking.cancelled' : 'booking.updated';
            
            // Queue SSE event for streaming to clients
            BookingStreamController::queueEvent($complexId, $eventType, [
                'booking_id' => $booking->id,
                'game_name' => $booking->game_name,
                'booking_date' => $booking->booking_date,
                'court_number' => $booking->court_number,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user_name,
                'status' => $booking->status,
                'complex_id' => $complexId,
                'changes' => $booking->getChanges(),
            ]);

            // Increment booking change counter for Livewire reactive updates
            $this->incrementChangeCounter($complexId);

            Log::info('Booking updated - SSE event queued', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'changes' => $booking->getChanges(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to queue booking updated event', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Increment the change counter for a complex
     * Used by Livewire components for reactive updates
     */
    private function incrementChangeCounter(int $complexId): void
    {
        $key = "booking_change_counter_{$complexId}";
        $counter = Cache::get($key, 0);
        Cache::put($key, $counter + 1, 3600); // 1 hour TTL
    }

    /**
     * Get the current change counter for a complex
     * Static method for use in Livewire components
     */
    public static function getChangeCounter(int $complexId): int
    {
        return Cache::get("booking_change_counter_{$complexId}", 0);
    }
}

