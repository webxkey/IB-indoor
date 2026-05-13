<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$col = DB::select("
    SELECT data_type
    FROM information_schema.columns
    WHERE table_name = 'booking_notification' AND column_name = 'data'
");

echo "Data type: " . $col[0]->data_type . "\n";
