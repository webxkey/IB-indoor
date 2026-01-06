<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingSport;
use App\Models\BookingVenue;

$venue = BookingVenue::find(6);
if (!$venue) {
    echo "Venue 6 not found\n";
    exit(1);
}

echo "Venue: {$venue->name} (ID: {$venue->id})\n\n";
echo "Sports for this venue:\n";

$sports = BookingSport::where('venue_id', $venue->id)->get();

if ($sports->isEmpty()) {
    echo "No sports found\n";
} else {
    foreach ($sports as $sport) {
        echo "- {$sport->name} (ID: {$sport->id}, Status: {$sport->status})\n";
    }
}
