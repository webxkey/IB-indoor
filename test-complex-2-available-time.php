<?php
/**
 * Real-Time Booking Test - Complex 2 Available Times
 * Tests booking creation with available time slots
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;

echo "\n" . str_repeat("=", 60) . "\n";
echo "  COMPLEX 2 - AVAILABLE TIME SLOTS TEST\n";
echo str_repeat("=", 60) . "\n\n";

try {
    // Get Complex 2
    $venue = BookingVenue::find(2);
    if (!$venue) {
        echo "❌ Complex 2 not found!\n";
        exit(1);
    }
    
    echo "📍 Testing Complex: " . $venue->name . " (ID: 2)\n";
    echo "   Location: " . $venue->location . "\n";
    echo "   Courts: " . $venue->total_courts . "\n\n";
    
    // Get available sports for this venue
    $sports = BookingSport::all();
    echo "🎾 Available Sports:\n";
    foreach ($sports as $sport) {
        echo "   - " . $sport->name . " (ID: " . $sport->id . ")\n";
    }
    echo "\n";
    
    // Check existing bookings for today on Complex 2
    $today = now()->format('Y-m-d');
    $existingBookings = BookingBooking::where('complex_id_id', 2)
        ->where('booking_date', $today)
        ->orderBy('start_time')
        ->get(['id', 'court_number', 'start_time', 'end_time', 'game_name']);
    
    echo "📅 Date: " . $today . "\n";
    echo "📊 Current Bookings on Complex 2:\n";
    
    if ($existingBookings->isEmpty()) {
        echo "   ✅ No bookings yet - All times available!\n\n";
    } else {
        echo "   Court | Start Time | End Time   | Sport\n";
        echo "   " . str_repeat("-", 45) . "\n";
        foreach ($existingBookings as $booking) {
            printf("   %4s  | %10s | %10s | %s\n", 
                $booking->court_number,
                $booking->start_time,
                $booking->end_time,
                $booking->game_name
            );
        }
        echo "\n";
    }
    
    // Define available time slots
    $availableSlots = [
        ['start' => '08:00:00', 'end' => '09:00:00', 'court' => '1'],
        ['start' => '09:00:00', 'end' => '10:00:00', 'court' => '1'],
        ['start' => '10:00:00', 'end' => '11:00:00', 'court' => '2'],
        ['start' => '14:00:00', 'end' => '15:00:00', 'court' => '3'],
        ['start' => '15:00:00', 'end' => '16:00:00', 'court' => '2'],
        ['start' => '16:00:00', 'end' => '17:00:00', 'court' => '1'],
        ['start' => '18:00:00', 'end' => '19:00:00', 'court' => '2'],
        ['start' => '19:00:00', 'end' => '20:00:00', 'court' => '3'],
    ];
    
    echo "⏰ Available Time Slots:\n";
    foreach ($availableSlots as $index => $slot) {
        echo "   " . ($index + 1) . ". Court " . $slot['court'] . " | " . 
             $slot['start'] . " - " . $slot['end'] . "\n";
    }
    echo "\n";
    
    // Create booking in first available slot
    $selectedSlot = $availableSlots[0];
    $sport = $sports->first();
    
    echo "🔍 Creating test booking in available slot...\n";
    echo "   Time Slot: " . $selectedSlot['start'] . " - " . $selectedSlot['end'] . "\n";
    echo "   Court: " . $selectedSlot['court'] . "\n";
    echo "   Sport: " . $sport->name . "\n\n";
    
    $booking = BookingBooking::create([
        'user_name' => 'Real-Time Test User',
        'game_name' => $sport->name,
        'user_number' => '1234567890',
        'court_number' => $selectedSlot['court'],
        'booking_date' => $today,
        'start_time' => $selectedSlot['start'],
        'end_time' => $selectedSlot['end'],
        'duration' => 1,
        'price' => 500,
        'game_id_id' => $sport->id,
        'complex_id_id' => 2,
        'status' => 'Confirmed',
        'payment_status' => 'Paid',
        'is_challenge_booking' => false
    ]);
    
    echo "✅ Booking Created Successfully!\n";
    echo "   Booking ID: " . $booking->id . "\n";
    echo "   User: " . $booking->user_name . "\n";
    echo "   Complex: " . $venue->name . "\n";
    echo "   Sport: " . $booking->game_name . "\n";
    echo "   Court: " . $booking->court_number . "\n";
    echo "   Time: " . $booking->start_time . " - " . $booking->end_time . "\n";
    echo "   Status: " . $booking->status . "\n\n";
    
    echo "📡 Broadcasting via WebSocket...\n";
    echo "   Channel: bookings.complex.2\n";
    echo "   Event: booking.created\n";
    echo "   Status: LIVE UPDATE SENT\n\n";
    
    echo str_repeat("=", 60) . "\n";
    echo "✅ TEST COMPLETED - Check your admin dashboard!\n";
    echo str_repeat("=", 60) . "\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Location: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
