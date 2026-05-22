<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = Schema::getColumnListing('booking_sport');
echo "=== columns of booking_sport ===\n";
print_r($columns);

$sport = DB::table('booking_sport')->first();
if ($sport) {
    echo "=== sample record ===\n";
    print_r($sport);
} else {
    echo "No sports found\n";
}
