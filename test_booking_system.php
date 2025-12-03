<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\BookingSport;
use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\UserUser;

echo "=== BOOKING SYSTEM DIAGNOSTIC ===\n\n";

// 1. Check table existence
echo "1. CHECKING TABLES\n";
echo "-------------------\n";
$tables = ['booking_booking', 'booking_sport', 'booking_venue', 'users_user'];
foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "   ✓ $table: " . ($exists ? "EXISTS" : "MISSING") . "\n";
}
echo "\n";

// 2. Check columns in booking_booking
echo "2. BOOKING_BOOKING TABLE STRUCTURE\n";
echo "------------------------------------\n";
if (Schema::hasTable('booking_booking')) {
    $columns = Schema::getColumnListing('booking_booking');
    foreach ($columns as $col) {
        echo "   • $col\n";
    }
} else {
    echo "   ✗ Table not found!\n";
}
echo "\n";

// 3. Check data existence
echo "3. DATA IN TABLES\n";
echo "-----------------\n";
$sportCount = BookingSport::count();
$venueCount = BookingVenue::count();
$userCount = UserUser::count();
$bookingCount = BookingBooking::count();

echo "   • BookingSport records: $sportCount\n";
echo "   • BookingVenue records: $venueCount\n";
echo "   • UserUser records: $userCount\n";
echo "   • BookingBooking records: $bookingCount\n";
echo "\n";

// 4. Sample sport data
echo "4. SAMPLE SPORTS\n";
echo "----------------\n";
$sports = BookingSport::limit(5)->get();
foreach ($sports as $sport) {
    echo "   Sport ID: {$sport->id}\n";
    echo "   • Name: {$sport->name}\n";
    echo "   • Venue ID: {$sport->venue_id}\n";
    echo "   • Status: {$sport->status}\n";
    echo "   • Price: {$sport->price}\n";
    echo "   • Courts: {$sport->maximum_court}\n\n";
}

// 5. Sample venue data
echo "5. SAMPLE VENUES\n";
echo "----------------\n";
$venues = BookingVenue::limit(3)->get();
foreach ($venues as $venue) {
    echo "   Venue ID: {$venue->id}\n";
    echo "   • Name: {$venue->name}\n";
    echo "   • Location: {$venue->location}\n\n";
}

// 6. Sample user data
echo "6. SAMPLE USERS\n";
echo "---------------\n";
$users = UserUser::limit(3)->get();
foreach ($users as $user) {
    echo "   User ID: {$user->id}\n";
    echo "   • Email: {$user->email}\n";
    echo "   • Name: {$user->name}\n\n";
}

// 7. Check foreign key constraints
echo "7. FOREIGN KEY CONSTRAINTS\n";
echo "----------------------------\n";
$fkQuery = "SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'booking_booking'";
$constraints = DB::select($fkQuery);
foreach ($constraints as $fk) {
    echo "   • {$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}
echo "\n";

// 8. Test booking creation
echo "8. TEST BOOKING CREATION\n";
echo "------------------------\n";
try {
    // Get a valid venue
    $venue = BookingVenue::first();
    if (!$venue) {
        echo "   ✗ No venue found in database\n";
    } else {
        echo "   ✓ Found venue: {$venue->name} (ID: {$venue->id})\n";
        
        // Get a sport for this venue
        $sport = BookingSport::where('venue_id', $venue->id)->first();
        if (!$sport) {
            echo "   ✗ No sport found for venue\n";
        } else {
            echo "   ✓ Found sport: {$sport->name} (ID: {$sport->id})\n";
            
            // Get a user
            $user = UserUser::first();
            if (!$user) {
                echo "   ✗ No user found in database\n";
            } else {
                echo "   ✓ Found user: {$user->email} (ID: {$user->id})\n";
                
                // Try to create a test booking
                echo "\n   Attempting to create a test booking...\n";
                $testBooking = BookingBooking::create([
                    'user_id_id' => $user->id,
                    'complex_id_id' => $venue->id,
                    'game_id_id' => $sport->id,
                    'game_name' => $sport->name,
                    'booking_date' => now()->format('Y-m-d'),
                    'start_time' => '10:00:00',
                    'end_time' => '11:00:00',
                    'duration' => 60,
                    'price' => $sport->price,
                    'payment_status' => 'Pending',
                    'payment_method' => 'Card',
                    'status' => 'Pending',
                    'user_name' => $user->email ?? 'Test User',
                    'user_number' => '1234567890',
                    'court_number' => '1',
                    'qr_code' => 'TEST_' . uniqid(),
                    'is_challenge_booking' => false,
                    'opponent_team_id' => null,
                    'team_id' => null,
                ]);
                
                echo "   ✓ Test booking created! ID: {$testBooking->id}\n";
                
                // Delete test booking
                $testBooking->delete();
                echo "   ✓ Test booking deleted\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Stack: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
