<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $complexId;

    /**
     * Create a new event instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
        if (isset($booking->complex_id_id)) {
            $this->complexId = $booking->complex_id_id;
        } elseif (isset($booking->pool) && isset($booking->pool->venue_id)) {
            $this->complexId = $booking->pool->venue_id;
        } elseif (isset($booking->pool_id)) {
            $pool = \App\Models\PoolsPool::find($booking->pool_id);
            $this->complexId = $pool ? $pool->venue_id : 1;
        } else {
            $this->complexId = $booking->venue_id ?? 1;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("bookings.complex.{$this->complexId}"),
            new Channel('bookings.global'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'user_name' => $this->booking->user_name ?? ($this->booking->user ? $this->booking->user->name : 'Guest'),
            'game_name' => isset($this->booking->pool_id) ? ($this->booking->pool ? $this->booking->pool->name : 'Swimming Pool') : ($this->booking->game_name ?? 'Sports'),
            'start_time' => $this->booking->start_time ?? ($this->booking->occurrence ? $this->booking->occurrence->start_time : null),
            'end_time' => $this->booking->end_time ?? ($this->booking->occurrence ? $this->booking->occurrence->end_time : null),
            'court_number' => $this->booking->court_number ?? 'Pool',
            'status' => $this->booking->status,
            'payment_status' => $this->booking->payment_status ?? 'Paid',
            'created_at' => $this->booking->created_at,
            'booking_date' => $this->booking->booking_date ?? ($this->booking->occurrence ? $this->booking->occurrence->session_date : null),
            'is_pool' => isset($this->booking->pool_id),
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }
}
