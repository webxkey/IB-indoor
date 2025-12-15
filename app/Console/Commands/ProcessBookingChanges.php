<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\BookingCreatedEvent;
use App\Events\BookingUpdatedEvent;
use App\Models\BookingBooking;

class ProcessBookingChanges extends Command
{
    protected $signature = 'bookings:process-changes';
    protected $description = 'Process booking changes from MySQL triggers and broadcast to WebSocket';

    public function handle()
    {
        $this->info('🚀 Starting booking change processor...');
        $this->info('📡 Monitoring database triggers for instant updates');
        $this->info('Press Ctrl+C to stop');
        $this->newLine();

        while (true) {
            try {
                // Get unprocessed changes
                $changes = DB::table('booking_change_queue')
                    ->where('processed', false)
                    ->orderBy('created_at', 'asc')
                    ->limit(50)
                    ->get();

                if ($changes->count() > 0) {
                    foreach ($changes as $change) {
                        $this->processChange($change);
                        
                        // Mark as processed
                        DB::table('booking_change_queue')
                            ->where('id', $change->id)
                            ->update(['processed' => true]);
                    }
                }

                // Small delay to avoid hammering the database
                usleep(100000); // 0.1 second
                
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(1);
            }
        }
    }

    private function processChange($change)
    {
        $data = json_decode($change->data, true);
        
        $this->line(sprintf(
            '[%s] %s booking #%d - Complex #%d',
            now()->format('H:i:s'),
            $change->action,
            $change->booking_id,
            $change->complex_id
        ));

        try {
            // Get the full booking model
            $booking = BookingBooking::find($change->booking_id);
            
            if (!$booking) {
                $this->warn("  ⚠️  Booking #{$change->booking_id} not found");
                return;
            }

            // Broadcast the appropriate event
            if ($change->action === 'INSERT') {
                broadcast(new BookingCreatedEvent($booking))->toOthers();
                $this->info("  ✅ Broadcasted BookingCreatedEvent");
            } elseif ($change->action === 'UPDATE') {
                broadcast(new BookingUpdatedEvent($booking))->toOthers();
                $this->info("  ✅ Broadcasted BookingUpdatedEvent");
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Broadcast failed: " . $e->getMessage());
        }
    }
}
