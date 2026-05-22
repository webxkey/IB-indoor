<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use Illuminate\Http\Request;

$user = User::where('email', 'mbamubacr7@gmail.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

$request = Request::create('/api/indoor-admin/dashboard/booking-report', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new \App\Http\Controllers\Api\Admin\DashboardController();
$response = $controller->bookingReport($request);

$data = json_decode($response->getContent(), true);

echo "=== Total Bookings in Response: " . count($data['bookings']) . " ===\n";
echo "=== Bookings in Response ===\n";
foreach ($data['bookings'] as $b) {
    if ($b['is_permanent']) {
        echo "ID: {$b['id']} | Date: {$b['booking_date']} | Time: {$b['start_time']} | Parent: {$b['permanent_source_id']} | Type: {$b['booking_type']} | Status: {$b['status']}\n";
    }
}
