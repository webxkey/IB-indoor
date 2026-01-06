<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Server-Sent Events (SSE) Controller for Real-Time Booking Updates
 * 
 * This provides efficient one-way server-to-client communication without
 * WebSockets. Updates are pushed to clients only when booking data changes.
 */
class BookingStreamController extends Controller
{
    /**
     * Stream booking updates via Server-Sent Events
     * 
     * @param Request $request
     * @param int $complexId
     * @return StreamedResponse
     */
    public function stream(Request $request, int $complexId): StreamedResponse
    {
        return new StreamedResponse(function () use ($complexId) {
            // Disable output buffering for real-time streaming
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set connection timeout to 30 seconds
            set_time_limit(30);

            // Initialize last check timestamp
            $lastEventId = Cache::get("booking_stream_{$complexId}_last_event", 0);
            $heartbeatInterval = 15; // Send heartbeat every 15 seconds
            $lastHeartbeat = time();

            echo "retry: 3000\n\n"; // Tell client to retry after 3 seconds on disconnect
            flush();

            while (true) {
                // Check for new booking events
                $events = $this->getNewEvents($complexId, $lastEventId);

                foreach ($events as $event) {
                    $this->sendEvent($event['type'], $event['data'], $event['id']);
                    $lastEventId = max($lastEventId, $event['id']);
                }

                // Send heartbeat to keep connection alive
                if (time() - $lastHeartbeat >= $heartbeatInterval) {
                    $this->sendEvent('heartbeat', ['timestamp' => time()]);
                    $lastHeartbeat = time();
                }

                // Check if client disconnected
                if (connection_aborted()) {
                    break;
                }

                // Sleep briefly to reduce CPU usage
                usleep(500000); // 0.5 seconds

                // Limit stream duration to 25 seconds (client will auto-reconnect)
                if (time() - $_SERVER['REQUEST_TIME'] > 25) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
        ]);
    }

    /**
     * Get new booking events since last check
     */
    private function getNewEvents(int $complexId, int $lastEventId): array
    {
        $events = [];
        $cacheKey = "booking_events_{$complexId}";
        
        $cachedEvents = Cache::get($cacheKey, []);
        
        foreach ($cachedEvents as $event) {
            if ($event['id'] > $lastEventId) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * Send an SSE event
     */
    private function sendEvent(string $type, array $data, ?int $id = null): void
    {
        if ($id !== null) {
            echo "id: {$id}\n";
        }
        echo "event: {$type}\n";
        echo "data: " . json_encode($data) . "\n\n";
        flush();
    }

    /**
     * Queue a booking event for streaming
     * Called by the BookingObserver when bookings change
     */
    public static function queueEvent(int $complexId, string $type, array $data): void
    {
        $cacheKey = "booking_events_{$complexId}";
        $events = Cache::get($cacheKey, []);
        
        // Generate unique event ID
        $eventId = (int)(microtime(true) * 1000);
        
        $events[] = [
            'id' => $eventId,
            'type' => $type,
            'data' => $data,
            'timestamp' => time(),
        ];

        // Keep only last 100 events and events from last 5 minutes
        $cutoff = time() - 300;
        $events = array_filter($events, fn($e) => $e['timestamp'] > $cutoff);
        $events = array_slice($events, -100);

        // Store events with 5-minute TTL
        Cache::put($cacheKey, $events, 300);
        
        // Update last event ID
        Cache::put("booking_stream_{$complexId}_last_event", $eventId, 300);

        Log::info("BookingStreamController: Event queued", [
            'complex_id' => $complexId,
            'type' => $type,
            'event_id' => $eventId,
        ]);
    }
}
