<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run()
    {
        // Sports for Elite Sports Center (complex_id = 1)
        Sport::create([
            'complex_id' => 1,
            'game_name' => 'Tennis',
            'game_type' => 'Outdoor',
            'rate_type' => 'Per hour',
            'price' => 2500.00,
            'maximum_court' => 6,
            'status' => 'Active',
            'game_image' => 'https://example.com/images/tennis.jpg',
            'description' => 'Professional outdoor tennis courts with floodlights for night play.',
            'additional_charges' => [
                'lighting' => 500.00,
                'racket_rental' => 300.00
            ],
            'advance_required' => true
        ]);

        Sport::create([
            'complex_id' => 1,
            'game_name' => 'Badminton',
            'game_type' => 'Indoor',
            'rate_type' => 'Per hour',
            'price' => 1800.00,
            'maximum_court' => 4,
            'status' => 'Active',
            'game_image' => 'https://example.com/images/badminton.jpg',
            'description' => 'Air-conditioned indoor badminton courts with professional flooring.'
        ]);

        Sport::create([
            'complex_id' => 1,
            'game_name' => 'Basketball',
            'game_type' => 'Indoor',
            'rate_type' => 'Per session',
            'price' => 5000.00,
            'maximum_court' => 2,
            'status' => 'Active',
            'game_image' => 'https://example.com/images/basketball.jpg',
            'description' => 'Full-size basketball court with bleachers for spectators.'
        ]);

        // Sports for Sunset Racquet Club (complex_id = 2)
        Sport::create([
            'complex_id' => 2,
            'game_name' => 'Squash',
            'game_type' => 'Indoor',
            'rate_type' => 'Per hour',
            'price' => 2000.00,
            'maximum_court' => 5,
            'status' => 'Active',
            'game_image' => 'https://example.com/images/squash.jpg',
            'description' => 'International standard squash courts with glass back walls.'
        ]);
    }
}