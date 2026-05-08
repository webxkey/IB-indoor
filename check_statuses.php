<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingBooking;

$statuses = BookingBooking::select('status')->distinct()->pluck('status');
echo "Distinct statuses: " . implode(', ', $statuses->toArray()) . "\n";

$latestCancelled = BookingBooking::where('status', 'like', 'cancel%')->latest()->first();
if ($latestCancelled) {
    echo "Latest cancelled booking status: '" . $latestCancelled->status . "'\n";
} else {
    echo "No cancelled bookings found.\n";
}
