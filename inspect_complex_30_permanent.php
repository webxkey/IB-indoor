<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$complexId = 30;

$bookings = DB::table('booking_booking')
    ->where('complex_id_id', $complexId)
    ->whereNotNull('permanent_source_id')
    ->get();

echo "=== Permanent Bookings in Complex 30: " . count($bookings) . " ===\n";
foreach ($bookings as $b) {
    echo "ID: {$b->id} | Date: {$b->booking_date} | Time: {$b->start_time} | Parent ID: {$b->permanent_source_id} | Status: {$b->status} | User: {$b->user_name} | Price: {$b->price}\n";
}

$parentBookings = DB::table('booking_permanentbooking')
    ->where('complex_id', $complexId)
    ->get();
echo "\n=== Permanent Parents in Complex 30: " . count($parentBookings) . " ===\n";
foreach ($parentBookings as $parent) {
    echo "Parent ID: {$parent->id} | User ID: {$parent->user_id} | Sport: {$parent->sport_id} | Config: {$parent->recurring_config}\n";
}
