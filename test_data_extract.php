<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$b = \App\Models\BookingBooking::latest('id')->first();
$secret = config('services.django_webhook.secret');

echo json_encode([
    'booking_id' => $b->id ?? null,
    'complex_id' => $b->complex_id_id ?? null,
    'sport_id' => $b->game_id_id ?? null,
    'date' => $b->booking_date ?? null,
    'time' => $b->start_time ?? null,
    'court' => $b->court_number ?? null,
    'secret' => $secret
], JSON_PRETTY_PRINT);
