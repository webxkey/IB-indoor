<?php

/**
 * Manually mark all past confirmed bookings as No-Show
 * Run this from the command line: php mark_noshow_manually.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use Carbon\Carbon;

echo "=== Marking Past Bookings as No-Show ===\n";
echo "Current Time: " . Carbon::now()->toDateTimeString() . "\n\n";

$now = Carbon::now();

// Get all Confirmed bookings from today or earlier
$confirmedBookings = BookingBooking::where('status', 'Confirmed')
    ->whereDate('booking_date', '<=', $now->toDateString())
    ->get();

echo "Found " . $confirmedBookings->count() . " Confirmed bookings on or before today\n\n";

$markedCount = 0;

foreach ($confirmedBookings as $booking) {
    try {
        // Extract just the date part from booking_date
        $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');

        // Parse end_time - handle both time strings and datetime strings
        $endTimeStr = $booking->end_time;
        if (strpos($endTimeStr, ' ') !== false) {
            // If it contains space, it might be a full datetime
            $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
        }

        $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
        $isPast = $bookingEnd->lessThan($now);

        echo sprintf(
            "ID: %d | Game: %s | Date: %s | End: %s | Past: %s\n",
            $booking->id,
            $booking->game_name ?: 'N/A',
            $bookingDate,
            $bookingEnd->format('Y-m-d H:i:s'),
            $isPast ? 'YES' : 'NO'
        );

        if ($isPast) {
            $booking->status = 'No-Show';
            $booking->save();
            $markedCount++;
            echo "  ✓ Marked as No-Show\n";
        }
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total bookings marked as No-Show: $markedCount\n";
echo "=== END ===\n";
