<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("
    SELECT column_name, is_nullable, data_type
    FROM information_schema.columns
    WHERE table_name = 'booking_permanentbooking'
");

foreach ($columns as $col) {
    echo "Column: {$col->column_name}, Nullable: {$col->is_nullable}, Type: {$col->data_type}\n";
}
