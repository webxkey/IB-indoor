<?php
/**
 * UNIVERSAL BROADCAST FIX
 * Triggers real-time updates for any bookings that were missed
 * This handles database inserts that bypassed the observer
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Events\BookingCreated;
use Illuminate\Support\Facades\Log;

echo "\n" . str_repeat("=", 70) . "\n";
echo "  MANUAL BROADCAST TRIGGER - FIX LIVE UPDATES\n";
echo str_repeat("=", 70) . "\n\n";

// Get all bookings from today
$today = now()->format('Y-m-d');
$todayBookings = BookingBooking::where('booking_date', $today)->get();

echo "📅 Bookings for today: " . $today . "\n";
echo "   Total: " . $todayBookings->count() . " bookings\n\n";

if ($todayBookings->count() === 0) {
    echo "❌ No bookings found for today!\n\n";
    exit(0);
}

// Show what we found
echo "Found Bookings:\n";
echo "   ID | User          | Complex | Time              | Status\n";
echo "   " . str_repeat("-", 65) . "\n";

foreach ($todayBookings as $booking) {
    printf("   %-2s | %-13s | %7s | %s - %s | %s\n",
        $booking->id,
        substr($booking->user_name, 0, 13),
        $booking->complex_id_id,
        $booking->start_time,
        $booking->end_time,
        $booking->status
    );
}

echo "\n";

// Trigger broadcasts
echo "🔄 Triggering broadcasts to all connected admin browsers...\n\n";

$successCount = 0;
$failCount = 0;

foreach ($todayBookings as $booking) {
    try {
        // Send the broadcast event
        broadcast(new BookingCreated($booking))->toOthers();
        
        // Log it
        Log::info('Manual broadcast triggered', [
            'booking_id' => $booking->id,
            'user_name' => $booking->user_name,
            'complex_id' => $booking->complex_id_id,
            'channel' => "bookings.complex.{$booking->complex_id_id}",
        ]);
        
        echo "   ✅ ID " . $booking->id . " → Channel: bookings.complex." . $booking->complex_id_id . "\n";
        $successCount++;
        
    } catch (\Exception $e) {
        echo "   ❌ ID " . $booking->id . " failed: " . $e->getMessage() . "\n";
        $failCount++;
    }
}

echo "\n";
echo str_repeat("=", 70) . "\n";
echo "RESULTS:\n";
echo str_repeat("=", 70) . "\n\n";

echo "✅ Successfully broadcasted: " . $successCount . " bookings\n";
if ($failCount > 0) {
    echo "❌ Failed: " . $failCount . " bookings\n";
}

echo "\n📱 WHAT JUST HAPPENED:\n";
echo "   • WebSocket events sent to all connected admin browsers\n";
echo "   • Event: 'booking.created'\n";
echo "   • Channels: " . $todayBookings->groupBy('complex_id_id')->keys()->implode(', bookings.complex.') . "\n";
echo "   • All admins should see the bookings appear NOW\n\n";

echo "📊 WHAT TO DO NOW:\n";
echo "   1. Go to your admin dashboard\n";
echo "   2. Check if you see the bookings (1:00 PM - 2:00 PM and 2:00 PM - 3:00 PM)\n";
echo "   3. If NOT visible:\n";
echo "      a. Press F5 to refresh the page\n";
echo "      b. Or Ctrl+Shift+Delete for full cache clear then F5\n";
echo "      c. Check browser console (F12) for errors\n\n";

echo "🎯 PREVENTION:\n";
echo "   To avoid this in the future, ALWAYS use the Laravel API:\n";
echo "   ✅ BookingBooking::create([...])\n";
echo "   ✅ Mobile app API endpoints\n";
echo "   ✅ Admin dashboard UI\n";
echo "   ❌ Direct SQL inserts\n";
echo "   ❌ Database manual inserts\n\n";

echo str_repeat("=", 70) . "\n\n";
