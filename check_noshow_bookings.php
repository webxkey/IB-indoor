<?php

/**
 * Quick diagnostic script to check No-Show bookings
 * Run this from the command line: php check_noshow_bookings.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use Carbon\Carbon;

echo "=== No-Show Diagnostic Check ===\n";
echo "Current Time: " . Carbon::now()->toDateTimeString() . "\n\n";

// Get all Confirmed bookings
$confirmedBookings = BookingBooking::where('status', 'Confirmed')
    ->whereDate('booking_date', '<=', Carbon::now()->toDateString())
    ->get();

echo "Total Confirmed Bookings: " . $confirmedBookings->count() . "\n\n";

if ($confirmedBookings->count() > 0) {
    echo "--- Confirmed Bookings ---\n";
    foreach ($confirmedBookings as $booking) {
        $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
        $bookingEnd = Carbon::parse($bookingDate . ' ' . $booking->end_time);
        $isPast = $bookingEnd->lessThan(Carbon::now());

        echo sprintf(
            "ID: %d | Game: %s | Date: %s | End: %s | Past: %s | Status: %s\n",
            $booking->id,
            $booking->game_name,
            $bookingDate,
            $bookingEnd->format('Y-m-d H:i:s'),
            $isPast ? 'YES (should be No-Show)' : 'NO',
            $booking->status
        );
    }
}

echo "\n--- No-Show Bookings ---\n";
$noShowBookings = BookingBooking::where('status', 'No-Show')->get();
echo "Total No-Show Bookings: " . $noShowBookings->count() . "\n";

if ($noShowBookings->count() > 0) {
    foreach ($noShowBookings as $booking) {
        $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
        echo sprintf(
            "ID: %d | Game: %s | Date: %s | End: %s | Status: %s\n",
            $booking->id,
            $booking->game_name,
            $bookingDate,
            $booking->end_time,
            $booking->status
        );
    }
}

echo "\n=== END ===\n";
