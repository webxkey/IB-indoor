<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$notif = DB::table('booking_notification')->latest()->first();
if ($notif) {
    echo "Type: " . $notif->type . "\n";
    echo "Data: " . $notif->data . "\n";
} else {
    echo "No notifications found.\n";
}
