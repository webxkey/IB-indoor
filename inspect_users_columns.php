<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== COLUMNS OF 'users' TABLE ===\n";
    $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users' AND table_schema = 'public';");
    foreach ($columns as $col) {
        echo "  {$col->column_name} ({$col->data_type})\n";
    }

    echo "\n=== ALL ROWS IN 'users' TABLE ===\n";
    $users = DB::table('users')->get();
    foreach ($users as $u) {
        $rowStr = [];
        foreach ($u as $col => $val) {
            $rowStr[] = "$col: " . (is_null($val) ? 'NULL' : $val);
        }
        echo implode(" | ", $rowStr) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
