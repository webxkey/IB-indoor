<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::orderBy('id', 'desc')->take(5)->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | Role: {$u->role} | Pass: " . substr($u->password, 0, 10) . "...\n";
}
