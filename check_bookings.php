<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$venueId = 30; // user venue

try {
    $perms = \App\Models\BookingPermanentbooking::where("complex_id", $venueId)->get();
    echo "PERMANENT BOOKINGS:\n";
    foreach($perms as $p) {
        echo "Sport: {$p->game_id_id} | Day: {$p->day_of_week} | Start: {$p->start_time} | End: {$p->end_time} | Court: {$p->court_number}\n";
    }
} catch (\Exception $e) {
    echo "No permanent bookings table or error: " . $e->getMessage() . "\n";
}

$regular = \App\Models\BookingBooking::where("complex_id_id", $venueId)
            ->where("booking_date", ">=", date("Y-m-d"))
            ->whereNotIn("status", ["Cancelled", "cancelled"])
            ->get();
echo "\nREGULAR UPCOMING BOOKINGS:\n";
foreach($regular as $r) {
    echo "Sport: {$r->game_id_id} | Date: {$r->booking_date} | Start: {$r->start_time} | Court: {$r->court_number}\n";
}

