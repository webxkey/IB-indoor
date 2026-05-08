<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;

$slots = BookingBooking::select('time_slot')->distinct()->limit(10)->pluck('time_slot');
echo "Distinct time slots: " . implode(', ', $slots->toArray()) . "\n";
