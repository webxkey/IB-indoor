<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;

$times = BookingBooking::select('start_time')->distinct()->limit(10)->pluck('start_time');
echo "Distinct start times: " . implode(', ', $times->toArray()) . "\n";
