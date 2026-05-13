<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;

$statuses = BookingBooking::select('status')->distinct()->pluck('status');
foreach ($statuses as $s) {
    echo "Status: '" . $s . "' (Length: " . strlen($s) . ")\n";
}
