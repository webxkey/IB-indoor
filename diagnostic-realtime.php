<?php
/**
 * Real-Time System Diagnostic
 * Checks if WebSocket events are being properly broadcast
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use Illuminate\Support\Facades\Log;

echo "\n" . str_repeat("=", 70) . "\n";
echo "  REAL-TIME BOOKING SYSTEM - DIAGNOSTIC\n";
echo str_repeat("=", 70) . "\n\n";

// 1. Check if Reverb is configured
echo "1️⃣  Checking Reverb Configuration...\n";
$broadcastDriver = config('broadcasting.default');
echo "   BROADCAST_DRIVER: " . $broadcastDriver . "\n";

if ($broadcastDriver !== 'reverb') {
    echo "   ❌ ERROR: BROADCAST_DRIVER is not set to 'reverb'!\n\n";
} else {
    echo "   ✅ BROADCAST_DRIVER is correctly set to 'reverb'\n\n";
}

// 2. Check Reverb config
echo "2️⃣  Checking Reverb Settings...\n";
$reverbConfig = config('broadcasting.connections.reverb');
echo "   App Key: " . ($reverbConfig['key'] ?? 'NOT SET') . "\n";
echo "   Host: " . (($reverbConfig['options']['host'] ?? null) ?: 'NOT SET') . "\n";
echo "   Port: " . (($reverbConfig['options']['port'] ?? null) ?: 'NOT SET') . "\n";
echo "   Scheme: " . (($reverbConfig['options']['scheme'] ?? null) ?: 'NOT SET') . "\n";

if ($reverbConfig && isset($reverbConfig['options']['host']) && isset($reverbConfig['options']['port'])) {
    echo "   ✅ Reverb is properly configured\n\n";
} else {
    echo "   ⚠️  Reverb configuration incomplete (using defaults)\n\n";
}

// 3. Check Observer Registration
echo "3️⃣  Checking Observer Registration...\n";
$observerRegistered = class_exists('App\\Observers\\BookingBookingObserver');
if ($observerRegistered) {
    echo "   ✅ BookingBookingObserver class exists\n";
} else {
    echo "   ❌ ERROR: BookingBookingObserver class not found!\n";
}

// Check AppServiceProvider
$providerPath = __DIR__ . '/app/Providers/AppServiceProvider.php';
if (file_exists($providerPath)) {
    $providerContent = file_get_contents($providerPath);
    if (strpos($providerContent, 'BookingBookingObserver') !== false && 
        strpos($providerContent, 'observe') !== false) {
        echo "   ✅ Observer is registered in AppServiceProvider\n\n";
    } else {
        echo "   ❌ ERROR: Observer is not registered in AppServiceProvider!\n\n";
    }
}

// 4. Check Event Classes
echo "4️⃣  Checking Event Classes...\n";
$eventFiles = [
    'BookingCreated' => __DIR__ . '/app/Events/BookingCreated.php',
    'BookingUpdated' => __DIR__ . '/app/Events/BookingUpdated.php',
    'BookingDeleted' => __DIR__ . '/app/Events/BookingDeleted.php',
];

foreach ($eventFiles as $name => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, 'ShouldBroadcast') !== false) {
            echo "   ✅ Event::" . $name . " implements ShouldBroadcast\n";
        } else {
            echo "   ❌ Event::" . $name . " does NOT implement ShouldBroadcast!\n";
        }
    }
}
echo "\n";

// 5. Check Channels Configuration
echo "5️⃣  Checking Channels Configuration...\n";
$channelsPath = __DIR__ . '/routes/channels.php';
if (file_exists($channelsPath)) {
    $channelsContent = file_get_contents($channelsPath);
    if (strpos($channelsContent, 'bookings.complex') !== false) {
        echo "   ✅ 'bookings.complex' channel is defined\n\n";
    } else {
        echo "   ❌ ERROR: 'bookings.complex' channel is not defined!\n\n";
    }
}

// 6. Check Laravel Echo in Bootstrap
echo "6️⃣  Checking Laravel Echo Configuration...\n";
$bootstrapPath = __DIR__ . '/resources/js/bootstrap.js';
if (file_exists($bootstrapPath)) {
    $bootstrapContent = file_get_contents($bootstrapPath);
    if (strpos($bootstrapContent, 'window.Echo') !== false && 
        strpos($bootstrapContent, 'reverb') !== false) {
        echo "   ✅ Laravel Echo is configured with Reverb\n\n";
    } else {
        echo "   ❌ ERROR: Laravel Echo is not properly configured!\n\n";
    }
}

// 7. Test broadcast by creating a booking
echo "7️⃣  Testing Broadcast by Creating Test Booking...\n";
try {
    $venue = BookingVenue::find(2);
    if (!$venue) {
        echo "   ❌ Complex 2 not found\n\n";
    } else {
        $sport = BookingSport::first();
        if (!$sport) {
            echo "   ❌ No sports found\n\n";
        } else {
            $booking = BookingBooking::create([
                'user_name' => 'Diagnostic Test User',
                'game_name' => $sport->name,
                'user_number' => '9999999999',
                'court_number' => '1',
                'booking_date' => now()->format('Y-m-d'),
                'start_time' => '20:00:00',
                'end_time' => '21:00:00',
                'duration' => 1,
                'price' => 500,
                'game_id_id' => $sport->id,
                'complex_id_id' => 2,
                'status' => 'Confirmed',
                'payment_status' => 'Paid',
                'is_challenge_booking' => false
            ]);
            
            echo "   ✅ Test booking created (ID: " . $booking->id . ")\n";
            echo "   📡 Event should be broadcast to: bookings.complex.2\n";
            echo "   🔔 Event name: booking.created\n\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// 8. Check recent logs
echo "8️⃣  Checking Recent Logs for Broadcast Activity...\n";
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = array_reverse(explode("\n", $logContent));
    
    $found = false;
    foreach (array_slice($lines, 0, 100) as $line) {
        if (strpos($line, 'broadcasted') !== false) {
            echo "   ✅ Found broadcast log entry\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "   ⚠️  No broadcast log entries found in recent logs\n";
        echo "      → Broadcasts might not be logging correctly\n";
        echo "      → Or broadcasts haven't happened yet\n";
    }
    echo "\n";
}

// 9. Summary
echo "9️⃣  Diagnostic Summary\n";
echo str_repeat("-", 70) . "\n\n";

echo "✅ System Components Checked:\n";
echo "   ✓ Reverb Configuration\n";
echo "   ✓ Observer Registration\n";
echo "   ✓ Event Classes\n";
echo "   ✓ Channels Configuration\n";
echo "   ✓ Laravel Echo Setup\n";
echo "   ✓ Broadcast Testing\n\n";

echo "📡 Next Steps:\n";
echo "   1. Open admin dashboard: http://127.0.0.1:8000\n";
echo "   2. Open browser DevTools (F12) → Console tab\n";
echo "   3. You should see: '✅ Laravel Echo initialized for real-time updates'\n";
echo "   4. Create a booking from mobile app or admin panel\n";
echo "   5. In DevTools Console, you should see:\n";
echo "      '📱 New Booking Created!' with booking details\n";
echo "   6. Check Network tab → WS tab (WebSocket)\n";
echo "      Should show connection to ws://127.0.0.1:8080\n\n";

echo "🔍 Debug Tips:\n";
echo "   • If no Console messages appear:\n";
echo "     → Check if 'npm run dev' is running\n";
echo "     → Check if assets were built: 'npm run build'\n";
echo "   • If WebSocket not connecting:\n";
echo "     → Verify 'php artisan reverb:start' is running\n";
echo "     → Check .env VITE_REVERB_* variables\n";
echo "   • If broadcasts not logging:\n";
echo "     → Check config/broadcasting.php\n";
echo "     → Verify APP_DEBUG=true for detailed logs\n\n";

echo str_repeat("=", 70) . "\n";
echo "Diagnostic complete! Check items above for any ❌ errors.\n";
echo str_repeat("=", 70) . "\n\n";
