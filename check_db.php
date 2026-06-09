<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== VENUES IN booking_venue ===\n";
    $venues = DB::table('booking_venue')->select('id', 'name', 'email_address')->orderBy('id', 'asc')->get();
    foreach ($venues as $v) {
        echo "ID: {$v->id} | Name: {$v->name} | Email: {$v->email_address}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
