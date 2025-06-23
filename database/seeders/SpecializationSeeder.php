<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialization; // <-- Ini adalah baris yang ditambahkan

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            'Kemeja Pria', 'Celana Panjang', 'Gaun Wanita', 'Kebaya',
            'Pakaian Anak', 'Batik', 'Jaket & Outerwear', 'Seragam Sekolah/Kantor',
            'Bordir', 'Payet & Manik-manik'
        ];
        
        // Menggunakan metode firstOrCreate untuk menghindari duplikasi data jika seeder dijalankan lebih dari sekali
        foreach ($specializations as $name) {
            Specialization::firstOrCreate(['name' => $name]);
        }
    }
}
