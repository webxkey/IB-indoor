<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Simulate a permanent booking request
$user = User::where('role', 'admin')->first();
$sport = BookingSport::first();

if (!$user || !$sport) {
    echo "Prerequisites missing.\n";
    exit;
}

$data = [
    'sport_id' => $sport->id,
    'venue_id' => $sport->venue_id, // Use sport's venue ID
    'booking_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
    'start_time' => '10:00:00',
    'end_time' => '11:00:00',
    'court_number' => '1',
    'user_name' => 'Test Permanent User v2',
    'user_number' => '0771234567',
    'price' => 1000,
    'is_permanent' => true,
    'permanent_weeks' => 4,
];

echo "Simulating permanent booking for 4 weeks...\n";

DB::beginTransaction();
try {
    $currentDate = Carbon::parse($data['booking_date']);
    $weeks = 4;
    $firstId = null;

    for ($i = 0; $i < $weeks; $i++) {
        $targetDate = $currentDate->format('Y-m-d');
        
        $booking = BookingBooking::create([
            'user_id_id' => $user->id,
            'game_id_id' => $data['sport_id'],
            'game_name' => $sport->name,
            'complex_id_id' => $data['venue_id'],
            'booking_date' => $targetDate,
            'date' => $targetDate,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'time_slot' => Carbon::parse($data['start_time'])->format('h:i A'),
            'court_number' => $data['court_number'],
            'user_name' => $data['user_name'],
            'user_number' => $data['user_number'],
            'duration' => 60,
            'price' => $data['price'],
            'payment_method' => 'cash',
            'payment_status' => 'Pending',
            'status' => 'confirmed',
            'is_challenge_booking' => false,
            'permanent_source_id' => $firstId,
        ]);

        if ($i === 0) {
            $firstId = $booking->id;
            $booking->update(['permanent_source_id' => $firstId]);
        }

        $currentDate->addWeek();
    }
    DB::commit();
    echo "SUCCESS: Created 4 bookings with source ID $firstId\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
