<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('role', 'admin')->first();
if ($user) {
    echo "Admin User ID: " . $user->id . "\n";
    echo "Admin Complex ID: '" . $user->complex_id . "'\n";
} else {
    echo "No admin found.\n";
}
