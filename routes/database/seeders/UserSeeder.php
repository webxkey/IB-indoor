<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 Delete existing staff record (if any)
        User::where('email', 'staff@gmail.com')->delete();

        // 🔥 Delete existing admin record (optional, if re-seeding admin too)
        User::where('email', 'admin@gmail.com')->delete();

        // Create Staff User
        User::create([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff@1213'),
            'role' => 'staff',
            'contact' => '0776657107',
            'complex_id' => 1, // Make sure complex ID 1 exists
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@1213'),
            'role' => 'admin',
            'contact' => '0717894272',
        ]);
    }
}
