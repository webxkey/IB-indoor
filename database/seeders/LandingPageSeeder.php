<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run()
    {
        $sections = [
            [
                'page_name' => 'home-hero',
                'section_title' => 'Welcome to Sportynix',
                'section_description' => 'The best indoor sports experience.',
                'images' => ['https://picsum.photos/seed/hero/1920/1080'],
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'page_name' => 'home-banner',
                'section_title' => 'Banner 1',
                'section_description' => 'Exciting offers and more.',
                'images' => ['https://picsum.photos/seed/banner1/800/400'],
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'page_name' => 'home-banner2',
                'section_title' => 'Banner 2',
                'section_description' => 'Join our community.',
                'images' => ['https://picsum.photos/seed/banner2/800/400'],
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'page_name' => 'home-banner3',
                'section_title' => 'Banner 3',
                'section_description' => 'Book your slots now.',
                'images' => ['https://picsum.photos/seed/banner3/800/400'],
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'page_name' => 'home-about',
                'section_title' => 'About Us',
                'section_description' => 'We provide the best facilities for indoor sports enthusiasts.',
                'images' => ['https://picsum.photos/seed/about/500/300'],
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'page_name' => 'home-social',
                'section_title' => 'Follow Us',
                'section_description' => 'Stay updated with our latest news on social media.',
                'images' => ['https://picsum.photos/seed/social/400/400'],
                'display_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            LandingPage::updateOrCreate(
                ['page_name' => $section['page_name']],
                $section
            );
        }
    }
}
