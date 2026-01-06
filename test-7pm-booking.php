<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use App\Events\BookingCreated;
use Carbon\Carbon;

// Get today's date
$today = Carbon::now()->format('Y-m-d');

// Get Venue 6 (Saman)
$venue = BookingVenue::find(6);
if (!$venue) {
    echo "❌ Venue not found!\n";
    exit(1);
}

// Get Football sport
$sport = BookingSport::where('name', 'Football')
    ->orWhere('name', 'football')
    ->first();

if (!$sport) {
    // Just get any first sport
    $sport = BookingSport::first();
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     CREATE 7PM-9PM BOOKING & BROADCAST LIVE                ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Create booking from 7pm to 9pm
echo "📝 Creating booking:\n";
echo "   Time: 19:00:00 - 21:00:00 (7PM - 9PM)\n";
echo "   Sport: Football\n";
echo "   Complex: Saman\n";
echo "   Court: 1\n\n";

try {
    $newBooking = BookingBooking::create([
        'user_name' => 'Live Test User ' . now()->timestamp,
        'game_name' => 'Football',
        'user_number' => '9999999999',
        'court_number' => '1',
        'booking_date' => $today,
        'start_time' => '19:00:00',
        'end_time' => '21:00:00',
        'duration' => 2,
        'price' => 1000,
        'game_id_id' => $sport->id,
        'complex_id_id' => $venue->id,
        'status' => 'Confirmed',
        'payment_status' => 'Paid',
        'is_challenge_booking' => false
    ]);

    echo "✅ Booking created successfully!\n";
    echo "   ID: {$newBooking->id}\n";
    echo "   User: {$newBooking->user_name}\n";
    echo "   Status: {$newBooking->status}\n\n";

    // Broadcast the new booking
    echo "📡 Broadcasting to all admin dashboards...\n";
    broadcast(new BookingCreated($newBooking))->toOthers();
    echo "✅ Broadcast sent!\n\n";

    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║         🎯 CHECK YOUR UI NOW - NEW BOOKING LIVE!           ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";

    echo "📊 New booking details:\n";
    echo "   ID: {$newBooking->id}\n";
    echo "   Time: 19:00:00 - 21:00:00\n";
    echo "   User: {$newBooking->user_name}\n";
    echo "   Status: LIVE UPDATE SENT\n\n";

    echo "✅ TEST COMPLETE!\n";
    echo "👀 Watch your UI for:\n";
    echo "   • Real-time notification popup\n";
    echo "   • New booking appearing at 7PM slot\n";
    echo "   • No page refresh needed!\n\n";

} catch (\Exception $e) {
    echo "❌ Error creating booking: {$e->getMessage()}\n";
    exit(1);
}
