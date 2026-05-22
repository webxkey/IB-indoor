<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function printTableSchema($tableName) {
    echo "\n=== Columns of '$tableName' ===\n";
    try {
        $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ? AND table_schema = 'public' ORDER BY ordinal_position;", [$tableName]);
        if (empty($columns)) {
            echo "Table '$tableName' does not exist.\n";
            return;
        }
        foreach ($columns as $col) {
            echo "  {$col->column_name} ({$col->data_type})\n";
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
}

printTableSchema('users');
printTableSchema('booking_venue');
printTableSchema('booking_sport');
printTableSchema('booking_booking');
