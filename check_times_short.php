<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;

$times = BookingBooking::whereRaw('LENGTH(start_time) < 8')->select('start_time')->distinct()->pluck('start_time');
echo "Short start times: " . implode(', ', $times->toArray()) . "\n";
