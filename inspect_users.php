<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function printTableSchema($tableName) {
    echo "\n--- Schema of '$tableName' ---\n";
    try {
        $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = ? AND table_schema = 'public';", [$tableName]);
        foreach ($columns as $col) {
            echo "  {$col->column_name} ({$col->data_type})\n";
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
}

try {
    printTableSchema('users');
    printTableSchema('venue_staff');
    printTableSchema('booking_venue');
    printTableSchema('booking_sport');

    echo "\n=== ADMIN/STAFF USERS IN users TABLE ===\n";
    $users = DB::table('users')->get();
    foreach ($users as $u) {
        $fields = [];
        foreach ($u as $k => $v) {
            $fields[] = "$k: " . (is_null($v) ? 'NULL' : $v);
        }
        echo implode(" | ", $fields) . "\n";
    }

    echo "\n=== VENUE STAFF IN venue_staff TABLE ===\n";
    if (Schema::hasTable('venue_staff')) {
        $staff = DB::table('venue_staff')->get();
        foreach ($staff as $s) {
            $fields = [];
            foreach ($s as $k => $v) {
                $fields[] = "$k: " . (is_null($v) ? 'NULL' : $v);
            }
            echo implode(" | ", $fields) . "\n";
        }
    } else {
        echo "venue_staff table does not exist.\n";
    }

    echo "\n=== VENUES IN booking_venue TABLE ===\n";
    $venues = DB::table('booking_venue')->get();
    foreach ($venues as $v) {
        $fields = [];
        foreach ($v as $k => $vVal) {
            $fields[] = "$k: " . (is_null($vVal) ? 'NULL' : $vVal);
        }
        echo implode(" | ", $fields) . "\n";
    }

    echo "\n=== RECENT BOOKINGS ===\n";
    $bookings = DB::table('booking_booking')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
    foreach ($bookings as $b) {
        echo "ID: {$b->id} | Date: {$b->booking_date} | Venue ID: " . ($b->venue_id ?? 'NULL') . " | Complex ID: " . ($b->complex_id_id ?? 'NULL') . " | User Name: {$b->user_name} | Price: {$b->price}\n";
    }

    echo "\n=== SPORTS ===\n";
    $sports = DB::table('booking_sport')->get();
    foreach ($sports as $sp) {
        echo "ID: {$sp->id} | Name: {$sp->name} | Venue ID: " . ($sp->venue_id ?? 'NULL') . " | Complex ID: " . ($sp->complex_id_id ?? 'NULL') . " | Is Active: " . ($sp->is_active ?? 'N/A') . "\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
