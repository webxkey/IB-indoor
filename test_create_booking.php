<?php

/**
 * Test Script to Create a New Booking (Simulating Mobile App)
 * 
 * This script simulates what happens when a mobile app creates a booking.
 * It will create a new booking in the database, which should trigger the
 * real-time update on the dashboard.
 * 
 * Usage: php test_create_booking.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\UserUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "   Testing Real-Time Booking Creation\n";
echo "==============================================\n\n";

// Configuration - Update these based on your needs
$complexId = 2; // Your BookingVenue ID
$gameName = 'Cricket'; // Change to your sport name
$courtNumber = '1';
$todayDate = Carbon::today()->format('Y-m-d');
$testTime = Carbon::now()->addMinutes(5)->format('H:00:00'); // Create booking 5 minutes from now
$userName = 'Test User from Mobile';
$userPhone = '+1234567890';

echo "Configuration:\n";
echo "- Complex ID: {$complexId}\n";
echo "- Game: {$gameName}\n";
echo "- Date: {$todayDate}\n";
echo "- Court: {$courtNumber}\n";
echo "- Time: {$testTime}\n";
echo "- User: {$userName}\n\n";

try {
    // Get sport details
    echo "Step 1: Finding sport details...\n";
    $sport = BookingSport::where('name', $gameName)
        ->where('venue_id', $complexId)
        ->where('status', 'Active')
        ->first();
    
    if (!$sport) {
        throw new Exception("Sport '{$gameName}' not found for complex {$complexId}");
    }
    echo "✓ Sport found: {$sport->name} (ID: {$sport->id})\n\n";
    
    // Get or create a test user
    echo "Step 2: Getting user...\n";
    $user = UserUser::first();
    if (!$user) {
        throw new Exception("No users found in database. Please create a user first.");
    }
    echo "✓ User found: {$user->email} (ID: {$user->id})\n\n";
    
    // Check if slot is already booked
    echo "Step 3: Checking if slot is available...\n";
    $existingBooking = BookingBooking::where('complex_id_id', $complexId)
        ->where('game_name', $gameName)
        ->where('booking_date', $todayDate)
        ->where('court_number', $courtNumber)
        ->where('start_time', $testTime)
        ->first();
    
    if ($existingBooking) {
        echo "⚠ Slot already booked! Deleting old booking...\n";
        $existingBooking->delete();
    }
    echo "✓ Slot is available\n\n";
    
    // Create the booking
    echo "Step 4: Creating new booking...\n";
    $endTime = Carbon::parse($testTime)->addMinutes(60)->format('H:i:s');
    
    $booking = BookingBooking::create([
        'user_id_id' => $user->id,
        'complex_id_id' => $complexId,
        'game_id_id' => $sport->id,
        'game_name' => $gameName,
        'booking_date' => $todayDate,
        'user_name' => $userName,
        'user_number' => $userPhone,
        'court_number' => $courtNumber,
        'start_time' => $testTime,
        'end_time' => $endTime,
        'duration' => 60,
        'price' => $sport->price_per_hour ?? 1800.00,
        'payment_status' => 'Pending',
        'payment_method' => 'Stripe',
        'status' => 'Booked',
        'notes' => 'Created by test script to simulate mobile app booking',
        'admin_comments' => '',
        'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
        'permanent' => false,
    ]);
    
    echo "✓ Booking created successfully!\n\n";
    
    echo "==============================================\n";
    echo "   Booking Details\n";
    echo "==============================================\n";
    echo "Booking ID: {$booking->booking_id}\n";
    echo "Game: {$booking->game_name}\n";
    echo "Court: {$booking->court_number}\n";
    echo "Date: {$booking->booking_date}\n";
    echo "Time: {$booking->start_time} - {$booking->end_time}\n";
    echo "User: {$booking->user_name} ({$booking->user_number})\n";
    echo "Status: {$booking->status}\n";
    echo "QR Code: {$booking->qr_code}\n";
    echo "==============================================\n\n";
    
    echo "✅ SUCCESS! The booking has been created.\n";
    echo "📱 This simulates what happens when a mobile app creates a booking.\n";
    echo "🔔 Check your dashboard - the new booking should appear automatically!\n";
    echo "   (without refreshing the page)\n\n";
    
    echo "If you see the booking on your dashboard, the real-time updates are working! 🎉\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
