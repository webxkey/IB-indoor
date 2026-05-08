<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$venues = DB::table('booking_venue')->get();
foreach ($venues as $v) {
    echo "Venue ID: " . $v->id . ", Name: " . $v->name . "\n";
}
