<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name'     => 'Admin FO',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // CEO
        User::create([
            'name'     => 'CEO FO',
            'email'    => 'ceo@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'ceo',
        ]);

        // Investor
        User::create([
            'name'     => 'Investor FO',
            'email'    => 'investor@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'investor',
        ]);

        // Penjahit Borongan
        User::create([
            'name'     => 'Penjahit FO',
            'email'    => 'penjahit@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'penjahit',
        ]);
    }
}
