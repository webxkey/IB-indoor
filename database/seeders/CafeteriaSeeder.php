<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CafeteriaCategory;
use App\Models\CafeteriaProduct;

class CafeteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $beverages = CafeteriaCategory::create([
            'name' => 'Beverages',
            'description' => 'Cold drinks, juices, soda and water bottles'
        ]);

        $snacks = CafeteriaCategory::create([
            'name' => 'Snacks',
            'description' => 'Chips, biscuits, candies and health bars'
        ]);

        $accessories = CafeteriaCategory::create([
            'name' => 'Sports Gear',
            'description' => 'Grips, wristbands, balls, shuttles and basic accessories'
        ]);

        // 2. Create Products
        // Beverages
        CafeteriaProduct::create([
            'category_id' => $beverages->id,
            'name' => 'Mineral Water 500ml',
            'code' => 'WTR500ML',
            'price' => 70.00,
            'wholesale_price' => 60.00,
            'distribute_price' => 55.00,
            'stock' => 120,
            'image' => 'https://images.unsplash.com/photo-1608885898957-a599fb1698d6?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        CafeteriaProduct::create([
            'category_id' => $beverages->id,
            'name' => 'Coca-Cola 500ml',
            'code' => 'COKE500',
            'price' => 180.00,
            'wholesale_price' => 165.00,
            'distribute_price' => 160.00,
            'stock' => 60,
            'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        CafeteriaProduct::create([
            'category_id' => $beverages->id,
            'name' => 'Red Bull Energy Drink',
            'code' => 'RDBL250',
            'price' => 650.00,
            'wholesale_price' => 600.00,
            'distribute_price' => 580.00,
            'stock' => 45,
            'image' => 'https://images.unsplash.com/photo-1622543956221-15bfae1d6462?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        // Snacks
        CafeteriaProduct::create([
            'category_id' => $snacks->id,
            'name' => 'Lays Classic Potato Chips',
            'code' => 'LAYSCLS',
            'price' => 220.00,
            'wholesale_price' => 200.00,
            'distribute_price' => 190.00,
            'stock' => 40,
            'image' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        CafeteriaProduct::create([
            'category_id' => $snacks->id,
            'name' => 'Snickers Chocolate Bar',
            'code' => 'SNIKR50',
            'price' => 280.00,
            'wholesale_price' => 260.00,
            'distribute_price' => 250.00,
            'stock' => 35,
            'image' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        // Sports Gear
        CafeteriaProduct::create([
            'category_id' => $accessories->id,
            'name' => 'Yonex Badminton Grip Tape',
            'code' => 'YONEXGRP',
            'price' => 350.00,
            'wholesale_price' => 310.00,
            'distribute_price' => 295.00,
            'stock' => 80,
            'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);

        CafeteriaProduct::create([
            'category_id' => $accessories->id,
            'name' => 'Mavis 350 Nylon Shuttles (6pcs)',
            'code' => 'MAVIS350',
            'price' => 1950.00,
            'wholesale_price' => 1800.00,
            'distribute_price' => 1750.00,
            'stock' => 15,
            'image' => 'https://images.unsplash.com/photo-1613918431201-49a7c36228a4?q=80&w=200&auto=format&fit=crop',
            'is_active' => true
        ]);
    }
}
