<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\BookingCreated;
use App\Models\BookingBooking;

class ListenToBookingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to PostgreSQL notifications for booking changes from mobile/external systems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Starting booking listener - polling for new bookings...');
        $this->info('⏰ Checking for new bookings every 2 seconds...');
        $this->info('Press Ctrl+C to stop listening');
        
        $processedIds = [];
        
        while (true) {
            try {
                // Check for recent unbroadcasted bookings (created in last 10 seconds)
                $recentBookings = BookingBooking::where('created_at', '>', now()->subSeconds(10))
                    ->orderBy('id', 'desc')
                    ->get();
                
                foreach ($recentBookings as $booking) {
                    // Skip if already processed in this session
                    if (in_array($booking->id, $processedIds)) {
                        continue;
                    }
                    
                    // Mark as processed
                    $processedIds[$booking->id] = true;
                    
                    // Broadcast the booking
                    $this->line("🎯 New booking detected: #{$booking->id} ({$booking->user_name})");
                    broadcast(new BookingCreated($booking))->toOthers();
                    
                    $this->info("✅ BROADCASTED: Booking #{$booking->id}");
                    Log::info('✅ BROADCASTED via listener: New booking from mobile/external system', [
                        'booking_id' => $booking->id,
                        'user_name' => $booking->user_name,
                        'complex_id' => $booking->complex_id_id,
                        'source' => 'polling_listener',
                    ]);
                }
                
                // Sleep for 2 seconds before next check
                sleep(2);
                
            } catch (\Exception $e) {
                Log::error('Booking listener error', ['error' => $e->getMessage()]);
                $this->error('❌ Error: ' . $e->getMessage());
                sleep(5);
            }
        }
    }

    /**
     * Process a database notification
     */
    private function processNotification($notification)
    {
        try {
            // Parse the notification payload
            $payload = json_decode($notification['message'], true);
            
            if (!$payload || empty($payload['action'])) {
                Log::warning('Invalid notification payload', ['data' => $notification['message']]);
                return;
            }
            
            $action = $payload['action'];
            $bookingId = $payload['booking_id'] ?? null;
            
            if (!$bookingId) {
                Log::warning('Notification missing booking_id', ['payload' => $payload]);
                return;
            }
            
            // Fetch the booking from database
            $booking = BookingBooking::find($bookingId);
            
            if (!$booking) {
                Log::warning('Booking not found', ['booking_id' => $bookingId]);
                return;
            }
            
            $this->line("🎯 Processing {$action} for booking #{$bookingId}");
            
            // Broadcast the event to connected clients
            if ($action === 'created') {
                broadcast(new BookingCreated($booking))->toOthers();
                $this->info("✅ BROADCASTED: Mobile booking #{$bookingId} ({$booking->user_name})");
                Log::info('✅ BROADCASTED via DATABASE TRIGGER: New booking from mobile system', [
                    'booking_id' => $bookingId,
                    'user_name' => $booking->user_name,
                    'complex_id' => $booking->complex_id_id,
                    'source' => 'postgresql_trigger',
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Error processing notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
