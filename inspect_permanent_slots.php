<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$permanentBookings = DB::table('booking_booking')
    ->whereNotNull('permanent_source_id')
    ->get();

echo "=== Permanent Bookings in DB count: " . count($permanentBookings) . " ===\n";
foreach ($permanentBookings as $pb) {
    echo "ID: {$pb->id} | Date: {$pb->booking_date} | Time: {$pb->start_time} | Parent ID: {$pb->permanent_source_id} | Venue ID: {$pb->complex_id_id} | Status: {$pb->status}\n";
}

$parentBookings = DB::table('booking_permanentbooking')->get();
echo "\n=== Permanent Parent Records count: " . count($parentBookings) . " ===\n";
foreach ($parentBookings as $parent) {
    echo "Parent ID: {$parent->id} | User ID: {$parent->user_id} | Sport: {$parent->sport_id} | Venue: {$parent->complex_id} | Start: {$parent->start_time}\n";
}
