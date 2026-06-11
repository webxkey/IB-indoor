<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users_user')->get();
echo "=== ALL ROWS IN 'users_user' TABLE ===\n";
foreach ($users as $user) {
    echo "id: " . $user->id . " | email: " . $user->email . " | is_staff: " . ($user->is_staff ? 'YES' : 'NO') . "\n";
}
