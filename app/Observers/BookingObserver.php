<?php

namespace App\Observers;

use App\Models\BookingBooking;
use App\Events\BookingCreatedEvent;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the BookingBooking "created" event.
     */
    public function created(BookingBooking $booking): void
    {
        // Broadcast the event when a new booking is created
        broadcast(new BookingCreatedEvent($booking))->toOthers();
        
        Log::info('BookingCreatedEvent broadcasted', [
            'booking_id' => $booking->booking_id,
            'complex_id' => $booking->complex_id_id,
        ]);
    }

    /**
     * Handle the BookingBooking "updated" event.
     */
    public function updated(BookingBooking $booking): void
    {
        // You can also broadcast updates if needed
        if ($booking->wasChanged('status')) {
            broadcast(new BookingCreatedEvent($booking))->toOthers();
            
            Log::info('Booking status updated and broadcasted', [
                'booking_id' => $booking->booking_id,
                'new_status' => $booking->status,
            ]);
        }
    }
}
