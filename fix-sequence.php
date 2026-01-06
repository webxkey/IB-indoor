<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Resetting database sequence...\n";

try {
    // Get max ID
    $maxId = DB::table('booking_booking')->max('id');
    echo "Max booking ID: " . $maxId . "\n";
    
    // Reset sequence
    DB::statement("SELECT setval(pg_get_serial_sequence('booking_booking', 'id'), " . ($maxId + 1) . ")");
    
    echo "✅ Sequence reset successfully!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
