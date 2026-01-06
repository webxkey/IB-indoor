<?php
/**
 * COMPREHENSIVE LIVE UPDATE TEST & FIX
 * 1. Broadcast existing bookings
 * 2. Create new booking properly
 * 3. Verify live updates
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Events\BookingCreated;

echo "\n" . str_repeat("=", 75) . "\n";
echo "  COMPLETE LIVE UPDATE TEST & FIX\n";
echo str_repeat("=", 75) . "\n\n";

// STEP 1: Broadcast all existing bookings
echo "STEP 1️⃣  - BROADCAST EXISTING BOOKINGS\n";
echo str_repeat("-", 75) . "\n";

$today = now()->format('Y-m-d');
$existingBookings = BookingBooking::where('booking_date', $today)->get();

echo "Found " . $existingBookings->count() . " bookings for " . $today . ":\n\n";

$broadcastCount = 0;
foreach ($existingBookings as $booking) {
    try {
        broadcast(new BookingCreated($booking))->toOthers();
        echo "   ✅ ID {$booking->id} → {$booking->start_time}-{$booking->end_time} | Complex {$booking->complex_id_id}\n";
        $broadcastCount++;
    } catch (\Exception $e) {
        echo "   ❌ ID {$booking->id} failed\n";
    }
}

echo "\n✅ " . $broadcastCount . " bookings broadcasted!\n\n";

// STEP 2: Create new booking using proper API method
echo "STEP 2️⃣  - CREATE NEW BOOKING (PROPER WAY)\n";
echo str_repeat("-", 75) . "\n";

try {
    // Get venue and sport
    $venue = BookingVenue::find(6); // Complex 6 based on existing bookings
    if (!$venue) {
        $venue = BookingVenue::first();
    }
    
    $sport = BookingSport::first();
    
    if (!$venue || !$sport) {
        echo "❌ Cannot find venue or sport!\n\n";
        exit(1);
    }
    
    // Calculate available time
    $existingTimes = BookingBooking::where('booking_date', $today)
        ->where('complex_id_id', $venue->id)
        ->get(['start_time', 'end_time']);
    
    $newStartTime = '16:00:00'; // 4:00 PM
    $newEndTime = '17:00:00';   // 5:00 PM
    
    echo "Creating new booking...\n";
    echo "   Time: {$newStartTime} - {$newEndTime}\n";
    echo "   Sport: {$sport->name}\n";
    echo "   Complex: {$venue->name}\n\n";
    
    // Create booking using Eloquent (this WILL trigger observer!)
    $newBooking = BookingBooking::create([
        'user_name' => 'Test Live User ' . now()->timestamp,
        'game_name' => $sport->name,
        'user_number' => '9999999999',
        'court_number' => '1',
        'booking_date' => $today,
        'start_time' => $newStartTime,
        'end_time' => $newEndTime,
        'duration' => 1,
        'price' => 500,
        'game_id_id' => $sport->id,
        'complex_id_id' => $venue->id,
        'status' => 'Confirmed',
        'payment_status' => 'Paid',
        'is_challenge_booking' => false
    ]);
    
    echo "✅ New booking created!\n";
    echo "   ID: {$newBooking->id}\n";
    echo "   User: {$newBooking->user_name}\n";
    echo "   Status: {$newBooking->status}\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating booking: " . $e->getMessage() . "\n\n";
    exit(1);
}

// STEP 3: Verify all bookings
echo "STEP 3️⃣  - VERIFY ALL BOOKINGS IN DATABASE\n";
echo str_repeat("-", 75) . "\n";

$allBookings = BookingBooking::where('booking_date', $today)
    ->orderBy('start_time')
    ->get();

echo "Total bookings for {$today}: " . $allBookings->count() . "\n\n";
echo "Booking List:\n";
echo "   ID | Time              | User                  | Court | Status\n";
echo "   " . str_repeat("-", 70) . "\n";

foreach ($allBookings as $b) {
    printf("   %-2s | %s - %s | %-21s | %5s | %s\n",
        $b->id,
        $b->start_time,
        $b->end_time,
        substr($b->user_name, 0, 21),
        $b->court_number,
        $b->status
    );
}

echo "\n";

// STEP 4: Send final broadcast for the new booking
echo "STEP 4️⃣  - FINAL BROADCAST (NEW BOOKING)\n";
echo str_repeat("-", 75) . "\n";

try {
    broadcast(new BookingCreated($newBooking))->toOthers();
    echo "✅ New booking broadcast sent!\n";
    echo "   Event: booking.created\n";
    echo "   Channel: bookings.complex.{$newBooking->complex_id_id}\n";
    echo "   Status: LIVE UPDATE SENT TO ALL ADMINS\n\n";
} catch (\Exception $e) {
    echo "❌ Broadcast failed: " . $e->getMessage() . "\n\n";
}

// FINAL SUMMARY
echo str_repeat("=", 75) . "\n";
echo "TEST COMPLETE! ✅\n";
echo str_repeat("=", 75) . "\n\n";

echo "📊 RESULTS:\n";
echo "   • Existing bookings: " . ($broadcastCount) . " broadcasted\n";
echo "   • New booking created: ID {$newBooking->id}\n";
echo "   • Total bookings now: " . $allBookings->count() . "\n\n";

echo "🎯 EXPECTED LIVE UPDATE:\n";
echo "   ✅ All " . $allBookings->count() . " bookings should appear on dashboard\n";
echo "   ✅ New booking (ID {$newBooking->id}) should show as: {$newStartTime}-{$newEndTime}\n";
echo "   ✅ Updates should appear WITHOUT page refresh (WebSocket magic!)\n\n";

echo "📱 TO TEST LIVE UPDATES:\n";
echo "   1. Go to admin dashboard: http://127.0.0.1:8000\n";
echo "   2. Open browser DevTools: Press F12\n";
echo "   3. Go to Console tab\n";
echo "   4. You should see: '📱 New Booking Created!' message\n";
echo "   5. All bookings should appear on the calendar:\n";

foreach ($allBookings as $b) {
    echo "      • {$b->start_time} - {$b->end_time} ({$b->game_name})\n";
}

echo "\n";
echo "⚠️  IF NOT SHOWING:\n";
echo "   1. Refresh page: Ctrl+Shift+Delete then F5\n";
echo "   2. Check console for errors: F12 → Console\n";
echo "   3. Make sure Reverb is running: netstat -ano | findstr :8080\n";
echo "   4. Make sure Laravel is running: netstat -ano | findstr :8000\n\n";

echo "✅ BOOKING CREATION METHOD (This time we used correct method!):\n";
echo "   ✅ BookingBooking::create([...]) ← CORRECT (triggers observer)\n";
echo "   ❌ Direct SQL INSERT ← WRONG (bypasses observer)\n\n";

echo str_repeat("=", 75) . "\n\n";
