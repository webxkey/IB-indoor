<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // Upcoming bookings
        Booking::create([
            'user_id' => 1,
            'complex_id' => 1,
            'game_id' => 1,
            'game_name' => 'Tennis',
            'user_name' => 'John Smith',
            'user_number' => '18765556789',
            'court_number' => 'Court 2',
            'permanent' => false,
            'booking_date' => Carbon::today()->addDays(2),
            'start_time' => '15:00:00',
            'end_time' => '16:30:00',
            'duration' => 90,
            'price' => 3750.00,
            'payment_status' => 'Paid',
            'payment_method' => 'Stripe',
            'status' => 'Upcoming',
            'notes' => 'Need two rackets and balls'
        ]);

        Booking::create([
            'user_id' => 2,
            'complex_id' => 1,
            'game_id' => 2,
            'game_name' => 'Badminton',
            'user_name' => 'Sarah Johnson',
            'user_number' => '18765557890',
            'court_number' => 'Court 1',
            'permanent' => true,
            'booking_date' => Carbon::today()->addDays(1),
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'duration' => 60,
            'price' => 1800.00,
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
            'status' => 'Upcoming',
            'qr_code' => 'BADM' . rand(1000, 9999)
        ]);

        // Completed bookings
        Booking::create([
            'user_id' => 2,
            'complex_id' => 2,
            'game_id' => 4,
            'game_name' => 'Squash',
            'user_name' => 'Michael Brown',
            'user_number' => '18765558901',
            'court_number' => 'Court 3',
            'permanent' => false,
            'booking_date' => Carbon::today()->subDays(3),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration' => 60,
            'price' => 2000.00,
            'payment_status' => 'Paid',
            'payment_method' => 'Card',
            'status' => 'Completed',
            'admin_comments' => 'Player arrived 10 mins early'
        ]);

        // Cancelled booking
        Booking::create([
            'user_id' => 1,
            'complex_id' => 1,
            'game_id' => 3,
            'game_name' => 'Basketball',
            'user_name' => 'John Smith',
            'user_number' => '18765556789',
            'permanent' => false,
            'booking_date' => Carbon::today()->addDays(5),
            'start_time' => '20:00:00',
            'end_time' => '22:00:00',
            'duration' => 120,
            'price' => 5000.00,
            'payment_status' => 'Failed',
            'payment_method' => 'Stripe',
            'status' => 'Cancelled',
            'notes' => 'Team booking cancelled due to rain forecast'
        ]);
    }
}
