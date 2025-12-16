<?php

namespace App\Observers;

use App\Models\BookingBooking;
use App\Events\BookingCreatedEvent;
use App\Events\BookingUpdatedEvent;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the BookingBooking "created" event.
     */
    public function created(BookingBooking $booking): void
    {
        try {
            // Broadcast the event when a new booking is created
            broadcast(new BookingCreatedEvent($booking))->toOthers();

            Log::info('BookingCreatedEvent broadcasted', [
                'booking_id' => $booking->id,
                'complex_id' => $booking->complex_id_id,
            ]);
        } catch (\Exception $e) {
            // Don't let broadcast failures affect the booking creation
            Log::warning('BookingCreatedEvent broadcast failed (WebSocket may be offline)', [
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
            // Broadcast when status or other important fields change
            if ($booking->wasChanged(['status', 'start_time', 'end_time', 'court_number'])) {
                broadcast(new BookingUpdatedEvent($booking))->toOthers();

                Log::info('BookingUpdatedEvent broadcasted', [
                    'booking_id' => $booking->id,
                    'changes' => $booking->getChanges(),
                    'status' => $booking->status,
                ]);
            }
        } catch (\Exception $e) {
            // Don't let broadcast failures affect the booking update
            Log::warning('BookingUpdatedEvent broadcast failed (WebSocket may be offline)', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
