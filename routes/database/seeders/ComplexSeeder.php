<?php

namespace Database\Seeders;

use App\Models\Complex;
use Illuminate\Database\Seeder;

class ComplexSeeder extends Seeder
{
    public function run()
    {
        Complex::create([
            'complex_name' => 'Elite Sports Center',
            'complex_type' => 'Mixed',
            'county' => 'Kingston',
            'location' => '18.0179° N, 76.8099° W',
            'address' => '123 Sports Avenue, Kingston 10',
            'postal_code' => 'JMAAW10',
            'contact_number' => '18765551234',
            'email_address' => 'info@elitesports.com',
            'website' => 'https://elitesports.com',
            'status' => 'Active',
            'opening_hours' => [
                'Monday' => '6:00 AM - 10:00 PM',
                'Tuesday' => '6:00 AM - 10:00 PM',
                'Wednesday' => '6:00 AM - 10:00 PM',
                'Thursday' => '6:00 AM - 10:00 PM',
                'Friday' => '6:00 AM - 11:00 PM',
                'Saturday' => '7:00 AM - 11:00 PM',
                'Sunday' => '8:00 AM - 9:00 PM'
            ],
            'amenities' => ['Wi-Fi', 'Parking', 'Lockers', 'Showers', 'Cafeteria', 'Pro Shop'],
            'cover_image' => 'https://example.com/images/elite-sports-center.jpg',
            'gallery_images' => [
                'https://example.com/images/gallery1.jpg',
                'https://example.com/images/gallery2.jpg'
            ],
            'video_tour_url' => 'https://youtube.com/elitesportstour',
            'description' => 'Premier sports facility with world-class amenities and multiple courts for various sports.',
            'terms' => 'All bookings require 24-hour cancellation notice. Proper sports attire required.',
            'social_links' => [
                'facebook' => 'https://facebook.com/elitesports',
                'instagram' => 'https://instagram.com/elitesports'
            ]
        ]);

        Complex::create([
            'complex_name' => 'Sunset Racquet Club',
            'complex_type' => 'Outdoor',
            'county' => 'St. Andrew',
            'location' => '18.0279° N, 76.8199° W',
            'address' => '45 Tennis Lane, Kingston 8',
            'postal_code' => 'JMAAW08',
            'contact_number' => '18765552345',
            'email_address' => 'bookings@sunsetracquet.com',
            'website' => 'https://sunsetracquet.com',
            'status' => 'Active',
            'opening_hours' => [
                'Monday' => '7:00 AM - 9:00 PM',
                'Tuesday' => '7:00 AM - 9:00 PM',
                'Wednesday' => '7:00 AM - 9:00 PM',
                'Thursday' => '7:00 AM - 9:00 PM',
                'Friday' => '7:00 AM - 10:00 PM',
                'Saturday' => '8:00 AM - 10:00 PM',
                'Sunday' => '8:00 AM - 8:00 PM'
            ],
            'amenities' => ['Parking', 'Lockers', 'Showers', 'Restaurant'],
            'cover_image' => 'https://example.com/images/sunset-racquet.jpg',
            'description' => 'Beautiful outdoor tennis and squash facility with 10 professional courts.'
        ]);
    }
}