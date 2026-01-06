<?php

namespace App\Observers;

use App\Events\BookingCreated;
use App\Events\BookingDeleted;
use App\Events\BookingUpdated;
use App\Models\BookingBooking;
use Illuminate\Support\Facades\Log;

class BookingBookingObserver
{
    /**
     * Handle the BookingBooking "created" event.
     */
    public function created(BookingBooking $booking): void
    {
        try {
            // Broadcast the new booking to all admins watching this complex
            broadcast(new BookingCreated($booking))->toOthers();
            
            Log::info('BookingBooking created and broadcasted', [
                'booking_id' => $booking->id,
                'user_name' => $booking->user_name,
                'complex_id' => $booking->complex_id_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast booking created event', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);
        }
    }

    /**
     * Handle the BookingBooking "updated" event.
     */
    public function updated(BookingBooking $booking): void
    {
        try {
            // Broadcast the updated booking
            broadcast(new BookingUpdated($booking))->toOthers();
            
            Log::info('BookingBooking updated and broadcasted', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'complex_id' => $booking->complex_id_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast booking updated event', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);
        }
    }

    /**
     * Handle the BookingBooking "deleted" event.
     */
    public function deleted(BookingBooking $booking): void
    {
        try {
            // Broadcast the deleted booking
            broadcast(new BookingDeleted($booking->id, $booking->complex_id_id ?? 1))->toOthers();
            
            Log::info('BookingBooking deleted and broadcasted', [
                'booking_id' => $booking->id,
                'complex_id' => $booking->complex_id_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast booking deleted event', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
            ]);
        }
    }
}
