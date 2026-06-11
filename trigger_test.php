<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BookingBooking;
use App\Events\SlotStateChanged;
use App\Events\BookingCreated;

$today = date('Y-m-d');

// -- 1. Prepare a mock booking for TODAY so you can see it in the UI immediately --
$existingBooking = BookingBooking::latest('id')->first();
$newBooking = $existingBooking->replicate();
$newBooking->booking_date = $today;
$newBooking->start_time = '16:00:00';
$newBooking->end_time = '17:00:00';
$newBooking->court_number = '1';
$newBooking->status = 'Confirmed';
$newBooking->user_name = 'Live Webhook Tester';
$newBooking->save();

// -- 2. Trigger HOLD Event for 15:00:00 today --
echo "Triggering [Slot Hold] for today at 15:00...\n";
$holdPayload = [
    'venue_id'   => $newBooking->complex_id_id,
    'sport_id'   => $newBooking->game_id_id,
    'event_type' => 'slot.hold.created',
    'date'       => $today,
    'start_time' => '15:00:00',
    'court'      => '1'
];
broadcast(new SlotStateChanged($holdPayload));
echo "✅ Hold event broadcasted to Reverb!\n\n";

sleep(2); // Pause so you can see the Hold before the Booking arrives

// -- 3. Trigger NEW BOOKING notification for 16:00:00 today --
echo "Triggering [Booking Notify] for today at 16:00...\n";
broadcast(new BookingCreated($newBooking));
echo "✅ New Booking event broadcasted to Reverb!\n";
