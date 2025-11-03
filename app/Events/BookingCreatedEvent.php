<?php

namespace App\Events;

use App\Models\BookingBooking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreatedEvent implements ShouldBroadcast
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
        $this->complexId = $booking->complex_id_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('bookings.' . $this->complexId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->booking_id,
            'game_name' => $this->booking->game_name,
            'booking_date' => $this->booking->booking_date,
            'court_number' => $this->booking->court_number,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'user_name' => $this->booking->user_name,
            'status' => $this->booking->status,
            'complex_id' => $this->complexId,
        ];
    }
}
