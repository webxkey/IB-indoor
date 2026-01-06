<?php
/**
 * API Broadcast Test Script
 * Tests the broadcast API endpoints
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Events\BookingCreated;

echo "\n" . str_repeat("=", 70) . "\n";
echo "  API BROADCAST TEST - DIRECT EXECUTION\n";
echo str_repeat("=", 70) . "\n\n";

// Get all today's bookings
$today = now()->format('Y-m-d');
$bookings = BookingBooking::where('booking_date', $today)->get();

echo "📅 Today's Bookings: " . $today . "\n";
echo "   Total Found: " . $bookings->count() . "\n\n";

if ($bookings->count() === 0) {
    echo "❌ No bookings found for today!\n\n";
    exit(0);
}

// Show bookings
echo "Bookings:\n";
foreach ($bookings as $b) {
    echo "   ID {$b->id}: {$b->user_name} | {$b->start_time}-{$b->end_time} | Complex {$b->complex_id_id}\n";
}
echo "\n";

// Broadcast all
echo "🔄 Broadcasting to all connected admin browsers...\n\n";

$count = 0;
foreach ($bookings as $booking) {
    try {
        broadcast(new BookingCreated($booking))->toOthers();
        echo "   ✅ Booking ID {$booking->id} broadcasted to complex {$booking->complex_id_id}\n";
        $count++;
    } catch (\Exception $e) {
        echo "   ❌ Booking ID {$booking->id} failed: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo str_repeat("=", 70) . "\n";
echo "✅ " . $count . " bookings broadcasted successfully!\n";
echo str_repeat("=", 70) . "\n\n";

echo "📱 WHAT TO DO NOW:\n";
echo "1. Go to your admin dashboard: http://127.0.0.1:8000\n";
echo "2. You should see both bookings appear:\n";
foreach ($bookings as $b) {
    echo "   - {$b->start_time} to {$b->end_time}\n";
}
echo "3. If not showing, press Ctrl+Shift+Delete then F5\n\n";
