<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'contact' => '1234567890',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@staff.com'],
            [
                'name' => 'Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'contact' => '0987654321',
            ]
        );
    }
}
