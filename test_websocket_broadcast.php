<?php

/**
 * Test WebSocket Broadcasting
 * Run: php test_websocket_broadcast.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Events\BookingCreatedEvent;
use Illuminate\Support\Facades\Log;

echo "=== WebSocket Broadcast Test ===\n\n";

// Get a complex ID - use 5 which is your staff user's complex
$complexId = 5; // Your staff user's complex ID

echo "Testing broadcast to channel: bookings.{$complexId}\n\n";

// Create a test booking
try {
    $booking = BookingBooking::create([
        'user_id_id' => 1,
        'complex_id_id' => $complexId,
        'game_id_id' => 1,
        'game_name' => 'football',
        'booking_date' => date('Y-m-d'),
        'court_number' => '1',
        'start_time' => '20:00:00',
        'end_time' => '21:00:00',
        'duration' => 60,
        'price' => 1800,
        'user_name' => 'WebSocket Test User',
        'user_number' => '0771234567',
        'status' => 'Confirmed',
        'payment_status' => 'Pending',
        'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
        'is_challenge_booking' => false,
        'notes' => '',
        'admin_comments' => '',
    ]);

    echo "✅ Booking created with ID: {$booking->id}\n";
    echo "✅ BookingObserver should have broadcast the event\n";
    echo "\n";
    echo "Check your staff dashboard - it should show the new booking!\n";
    echo "Check browser console for WebSocket messages.\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
