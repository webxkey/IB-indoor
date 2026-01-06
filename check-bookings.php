<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;

$bookings = BookingBooking::where('booking_date', '2026-01-06')->get(['id', 'user_name', 'complex_id_id', 'start_time', 'end_time', 'status']);

echo "Bookings for 2026-01-06:\n";
foreach ($bookings as $b) {
    echo "ID: {$b->id} | User: {$b->user_name} | Complex: {$b->complex_id_id} | Time: {$b->start_time}-{$b->end_time}\n";
}
