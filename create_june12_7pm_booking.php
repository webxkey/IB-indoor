<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\Admin\BookingController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Simulate an authenticated admin/staff user
$user = User::where('role', 'facility_owner')->where('complex_id', 2)->first();
Auth::login($user);

$request = Request::create('/api/indoor-admin/bookings/create', 'POST', [
    'sport_id' => 20, // Cricket & Football
    'venue_id' => 2,
    'booking_date' => '2026-06-12',
    'customer_name' => 'Held Slot Tester 2',
    'customer_phone' => '0771234567',
    'slots' => [
        [
            'start_time' => '19:00:00',
            'end_time' => '20:00:00',
            'price' => 2000,
            'duration' => 60
        ]
    ],
    'court_number' => '1',
    'payment_method' => 'Cash',
    'payment_status' => 'Pending',
    'is_permanent' => false
]);

$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new BookingController();
$response = $controller->create($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Response Body: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
