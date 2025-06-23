<?php
// Path: database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Menggunakan firstOrCreate untuk menghindari error duplikat
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Kunci untuk mencari
            [ // Data yang akan dibuat jika tidak ditemukan
                'name'     => 'Admin FO',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'ceo@example.com'],
            [
                'name'     => 'CEO FO',
                'password' => Hash::make('password123'),
                'role'     => 'ceo',
            ]
        );

        User::firstOrCreate(
            ['email' => 'investor@example.com'],
            [
                'name'     => 'Investor FO',
                'password' => Hash::make('password123'),
                'role'     => 'investor',
            ]
        );

        User::firstOrCreate(
            ['email' => 'penjahit@example.com'],
            [
                'name'     => 'Penjahit FO',
                'password' => Hash::make('password123'),
                'role'     => 'penjahit',
            ]
        );
    }
}
