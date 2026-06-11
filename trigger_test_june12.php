<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BookingBooking;
use App\Events\SlotStateChanged;
use App\Events\BookingCreated;

$targetDate = '2026-06-12';
$sportId = 20; // Cricket & Football
$venueId = 2;

echo "Preparing test for {$targetDate} on Cricket & Football...\n";

// -- 1. Prepare a mock booking for 2026-06-12 so you can see it in the UI immediately --
$existingBooking = BookingBooking::latest('id')->first();
$newBooking = $existingBooking ? $existingBooking->replicate() : new BookingBooking();

$newBooking->booking_date = $targetDate;
$newBooking->start_time = '22:00:00';
$newBooking->end_time = '23:00:00';
$newBooking->court_number = '1';
$newBooking->status = 'Confirmed';
$newBooking->user_name = 'Live Transition Tester (10PM)';
$newBooking->game_id_id = $sportId;
$newBooking->complex_id_id = $venueId;
$newBooking->game_name = 'Cricket & Football';

// Make sure required fields have default values if existingBooking was empty
if (!$newBooking->user_number) $newBooking->user_number = '1234567890';

$newBooking->save();

// -- 2. Trigger HOLD Event for 22:00:00 on June 12 --
echo "Triggering [Slot Hold] for {$targetDate} at 22:00...\n";
$holdPayload = [
    'venue_id'   => $venueId,
    'sport_id'   => $sportId,
    'event_type' => 'slot.hold.created',
    'date'       => $targetDate,
    'start_time' => '22:00:00',
    'court'      => '1'
];
broadcast(new SlotStateChanged($holdPayload));
echo "✅ Hold event broadcasted! Slot is now ORANGE.\n";

echo "Waiting 4 seconds so you can see the hold...\n";
sleep(4); 

// -- 3. Trigger NEW BOOKING notification for 22:00:00 on June 12 --
echo "Triggering [Booking Notify] for {$targetDate} at 22:00...\n";

// Mimic the Django webhook payload which broadcasts over the slot channel
$bookingPayload = [
    'venue_id'   => $venueId,
    'sport_id'   => $sportId,
    'event_type' => 'booking.created',
    'date'       => $targetDate,
    'start_time' => '22:00:00',
    'court'      => '1'
];
broadcast(new SlotStateChanged($bookingPayload));

// Also fire the Laravel event just in case
broadcast(new BookingCreated($newBooking));

echo "✅ New Booking event broadcasted! Slot should now be RED/BOOKED.\n";
