<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlotStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly array $data) {}

    public function broadcastOn(): array
    {
        $venueId = $this->data['venue_id'] ?? 0;
        $sportId = $this->data['sport_id'] ?? 0;

        return [
            new Channel("slots.venue.{$venueId}.sport.{$sportId}"),
            new Channel('slots.global'),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }

    public function broadcastAs(): string
    {
        return 'slot_state_changed';
    }
}
