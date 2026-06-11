<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "QUEUE_CONNECTION: " . config('queue.default') . "\n";
echo "BROADCAST_DRIVER: " . config('broadcasting.default') . "\n";
