<?php

namespace App\Events;

use App\Models\BookingBooking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $complexId;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingBooking $booking)
    {
        $this->booking = $booking;
        $this->complexId = $booking->complex_id_id ?? $booking->venue_id ?? 1;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("bookings.complex.{$this->complexId}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'user_name' => $this->booking->user_name,
            'game_name' => $this->booking->game_name,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'court_number' => $this->booking->court_number,
            'status' => $this->booking->status,
            'payment_status' => $this->booking->payment_status,
            'updated_at' => $this->booking->updated_at,
            'booking_date' => $this->booking->booking_date,
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'booking.updated';
    }
}
