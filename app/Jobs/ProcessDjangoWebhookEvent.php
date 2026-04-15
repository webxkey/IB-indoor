<?php

namespace App\Jobs;

use App\Events\SlotStateChanged;
use App\Models\DjangoWebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDjangoWebhookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $webhookEventId) {}

    public function handle(): void
    {
        $record = DjangoWebhookEvent::find($this->webhookEventId);
        if (!$record || $record->status === 'processed') {
            return;
        }

        try {
            $envelope = $record->payload_json;
            $payload  = $envelope['payload'] ?? [];

            $broadcastData = [
                'type'             => 'slot_state_changed',
                'event_type'       => $envelope['event_type'],
                'sport_id'         => $payload['sport_id'] ?? null,
                'venue_id'         => $payload['venue_id'] ?? null,
                'date'             => $payload['date'] ?? null,
                'start_time'       => $payload['start_time'] ?? null,
                'end_time'         => $payload['end_time'] ?? null,
                'court'            => $payload['court'] ?? null,
                'available'        => $payload['available'] ?? null,
                'available_courts' => $payload['available_courts'] ?? null,
                'total_courts'     => $payload['total_courts'] ?? null,
                'reason'           => $payload['reason'] ?? null,
                'booking_id'       => $payload['booking_id'] ?? null,
                'hold_id'          => $payload['hold_id'] ?? null,
                'actor'            => $envelope['payload']['actor'] ?? null,
                'occurred_at'      => $envelope['occurred_at'] ?? null,
                'correlation_id'   => $envelope['correlation_id'] ?? null,
            ];

            broadcast(new SlotStateChanged($broadcastData));

            $record->update([
                'status'       => 'processed',
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $record->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('ProcessDjangoWebhookEvent failed', [
                'webhook_event_id' => $this->webhookEventId,
                'error'            => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
