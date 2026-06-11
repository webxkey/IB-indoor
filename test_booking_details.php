<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$comp = new \App\Livewire\Staff\BookingsManagement();
$comp->complex_id = 2;
$data = $comp->getBookingDetails();

$game = 'cricket & football';
$date = '2026-06-12';
$court = '1';

if (isset($data[$game][$date][$court])) {
    echo "Bookings for $game on $date, court $court:\n";
    foreach ($data[$game][$date][$court] as $time => $details) {
        echo " - $time : " . $details['player'] . " (" . $details['status'] . ")\n";
    }
} else {
    echo "No bookings found for $game on $date, court $court.\n";
}
