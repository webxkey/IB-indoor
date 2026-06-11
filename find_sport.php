<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sport = \App\Models\BookingSport::where('name', 'like', '%Cricket%')->first();
echo json_encode([
    'id' => $sport->id ?? null,
    'name' => $sport->name ?? null,
    'venue_id' => $sport->venue_id ?? null
]);
