<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BookingBooking;
use App\Events\BookingCreatedEvent;
use App\Events\BookingUpdatedEvent;
use Illuminate\Support\Facades\Log;

class ProcessDatabaseNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:process-notifications';

    /**
     * The console command description.
     */
    protected $description = 'Process database triggers and broadcast WebSocket events for direct DB changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting database notification processor...');
        $this->info('📡 Monitoring for direct database changes (phpMyAdmin, SQL, etc.)');
        $this->newLine();

        $processedCount = 0;

        while (true) {
            try {
                // Get unprocessed notifications
                $notifications = DB::table('booking_notifications')
                    ->where('processed', false)
                    ->orderBy('created_at', 'asc')
                    ->limit(100)
                    ->get();

                foreach ($notifications as $notification) {
                    // Get the booking
                    $booking = BookingBooking::find($notification->booking_id);

                    if (!$booking) {
                        // Mark as processed even if booking not found
                        DB::table('booking_notifications')
                            ->where('id', $notification->id)
                            ->update(['processed' => true]);
                        continue;
                    }

                    // Broadcast the appropriate event
                    if ($notification->event_type === 'created') {
                        broadcast(new BookingCreatedEvent($booking))->toOthers();
                        $this->line("✅ Broadcasted: Booking #{$booking->id} created (Direct DB)");
                        Log::info('BookingCreatedEvent broadcasted from DB trigger', [
                            'booking_id' => $booking->id,
                            'source' => 'database_trigger'
                        ]);
                    } elseif ($notification->event_type === 'updated') {
                        broadcast(new BookingUpdatedEvent($booking))->toOthers();
                        $this->line("🔄 Broadcasted: Booking #{$booking->id} updated (Direct DB)");
                        Log::info('BookingUpdatedEvent broadcasted from DB trigger', [
                            'booking_id' => $booking->id,
                            'source' => 'database_trigger'
                        ]);
                    }

                    // Mark as processed
                    DB::table('booking_notifications')
                        ->where('id', $notification->id)
                        ->update(['processed' => true]);

                    $processedCount++;
                }

                // Clean up old processed notifications (keep last 1000)
                if ($processedCount % 100 === 0 && $processedCount > 0) {
                    DB::table('booking_notifications')
                        ->where('processed', true)
                        ->where('created_at', '<', now()->subDays(7))
                        ->delete();
                }

                // Sleep for 1 second before checking again
                usleep(1000000); // 1 second

            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                Log::error('Database notification processor error', [
                    'error' => $e->getMessage()
                ]);
                sleep(5); // Wait 5 seconds on error
            }
        }

        return 0;
    }
}
