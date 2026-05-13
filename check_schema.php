<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'booking_booking';
$columns = DB::select("
    SELECT column_name, is_nullable, column_default, data_type
    FROM information_schema.columns
    WHERE table_name = '$table'
");

foreach ($columns as $col) {
    echo "Column: {$col->column_name}, Nullable: {$col->is_nullable}, Default: {$col->column_default}, Type: {$col->data_type}\n";
}
