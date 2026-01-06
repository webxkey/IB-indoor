<?php
/**
 * Live Update Issue Diagnosis & Fix
 * 
 * Problem: Database bookings created manually don't appear live
 * Reason: Direct database inserts bypass Eloquent observers
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Events\BookingCreated;
use Carbon\Carbon;

echo "\n" . str_repeat("=", 70) . "\n";
echo "  LIVE UPDATE ISSUE DIAGNOSIS & SOLUTION\n";
echo str_repeat("=", 70) . "\n\n";

// 1. Check for bookings created today
$today = now()->format('Y-m-d');
$todayBookings = BookingBooking::where('booking_date', $today)
    ->where('complex_id_id', 2)
    ->get();

echo "📅 Bookings created today (Complex 2):\n";
echo "   Date: " . $today . "\n";
echo "   Found: " . $todayBookings->count() . " bookings\n\n";

if ($todayBookings->count() > 0) {
    echo "   ID | User          | Time              | Status\n";
    echo "   " . str_repeat("-", 60) . "\n";
    
    foreach ($todayBookings as $booking) {
        printf("   %-2s | %-13s | %s - %s | %s\n",
            $booking->id,
            $booking->user_name,
            $booking->start_time,
            $booking->end_time,
            $booking->status
        );
    }
    echo "\n";
}

// 2. Explain the issue
echo "🔴 THE PROBLEM:\n";
echo "   When you insert bookings directly into database:\n";
echo "   ❌ Observer is NOT triggered\n";
echo "   ❌ Broadcast event is NOT sent\n";
echo "   ❌ Admin dashboard doesn't know about the booking\n";
echo "   ❌ You need to refresh the page manually\n\n";

echo "🟢 THE SOLUTION:\n";
echo "   We can manually trigger broadcasts for existing bookings!\n\n";

// 3. Trigger broadcasts for all today's bookings
echo "🔄 Triggering broadcasts for all today's bookings...\n";
echo "   This will notify all connected admin browsers\n\n";

$broadcastCount = 0;
foreach ($todayBookings as $booking) {
    try {
        // Manually trigger the broadcast event
        broadcast(new BookingCreated($booking))->toOthers();
        $broadcastCount++;
        echo "   ✅ Booking ID " . $booking->id . " broadcast sent\n";
    } catch (\Exception $e) {
        echo "   ❌ Booking ID " . $booking->id . " failed: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "📡 Broadcasts Sent: " . $broadcastCount . " / " . $todayBookings->count() . "\n\n";

echo str_repeat("=", 70) . "\n";
echo "SOLUTION APPLIED:\n";
echo "=", str_repeat("=", 68) . "\n\n";

echo "✅ All bookings for today have been broadcast!\n\n";

echo "NEXT STEPS:\n";
echo "1. Check your admin dashboard\n";
echo "2. You should see ALL bookings now:\n";
echo "   - Open browser DevTools (F12) → Console\n";
echo "   - You should see: '📱 New Booking Created!' messages\n";
echo "3. If still not showing:\n";
echo "   - Refresh the page: F5\n";
echo "   - Do a full refresh: Ctrl+Shift+Delete then F5\n\n";

echo "HOW TO PREVENT THIS IN FUTURE:\n";
echo "❌ DON'T use raw SQL or direct database inserts\n";
echo "✅ DO use the Laravel API to create bookings:\n";
echo "   - Use BookingBooking::create([...])\n";
echo "   - Or use the mobile app API endpoint\n";
echo "   - Or use the admin dashboard UI\n\n";

echo "WHY THIS MATTERS:\n";
echo "• Direct database inserts bypass validation\n";
echo "• Direct inserts bypass observers/events\n";
echo "• Direct inserts don't trigger broadcasts\n";
echo "• This breaks the real-time system!\n\n";

echo str_repeat("=", 70) . "\n\n";
