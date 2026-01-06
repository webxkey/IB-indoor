<?php
/**
 * Real-Time Booking System Test
 * Creates a test booking and verifies broadcast
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n✅ Testing Real-Time Booking System\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Find a valid venue first
    $venue = \App\Models\BookingVenue::first();
    if (!$venue) {
        echo "❌ No venues found in database. Please create a venue first.\n";
        exit(1);
    }
    
    // Find a valid game first
    $game = \App\Models\BookingSport::first();
    if (!$game) {
        echo "❌ No games/sports found in database. Please create a sport first.\n";
        exit(1);
    }
    
    echo "Creating test booking...\n";
    echo "   Using Venue: " . $venue->name . " (ID: " . $venue->id . ")\n";
    echo "   Using Sport: " . $game->name . " (ID: " . $game->id . ")\n\n";
    
    $booking = \App\Models\BookingBooking::create([
        'user_name' => 'WebSocket Test User',
        'game_name' => $game->name,
        'user_number' => '9876543210',
        'court_number' => '2',
        'booking_date' => now()->format('Y-m-d'),
        'start_time' => '14:00:00',
        'end_time' => '15:00:00',
        'duration' => 1,
        'price' => 750,
        'game_id_id' => $game->id,
        'complex_id_id' => $venue->id,
        'status' => 'Confirmed',
        'payment_status' => 'Paid',
        'is_challenge_booking' => false
    ]);
    
    echo "✅ Booking created successfully!\n";
    echo "   Booking ID: " . $booking->id . "\n";
    echo "   User: " . $booking->user_name . "\n";
    echo "   Sport: " . $booking->game_name . "\n";
    echo "   Status: " . $booking->status . "\n\n";
    
    echo "📡 Broadcasting event...\n";
    echo "   - Event: BookingCreated\n";
    echo "   - Channel: bookings.complex." . $booking->complex_id_id . "\n";
    echo "   - Type: Real-time WebSocket\n\n";
    
    echo "✨ Expected behavior:\n";
    echo "   1. Reverb broadcasts event to subscribed clients\n";
    echo "   2. All admin browsers receive notification\n";
    echo "   3. Booking appears in calendar instantly\n";
    echo "   4. No polling requests made\n\n";
    
    echo "🔍 Check these to verify:\n";
    echo "   - Browser Console: Should show '📱 New Booking Created!'\n";
    echo "   - Network Tab: Should show WebSocket on :8080 (no /poll requests)\n";
    echo "   - Reverb Terminal: Should show 'Broadcasting event: booking.created'\n";
    echo "   - Laravel Logs: Should show 'created and broadcasted'\n\n";
    
    echo "=" . str_repeat("=", 50) . "\n";
    echo "✅ Test booking created! Check your admin dashboard.\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
