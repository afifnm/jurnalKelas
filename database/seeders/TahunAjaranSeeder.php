<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::create(['nama' => '2024/2025', 'semester' => 'Genap',  'is_aktif' => false]);
        TahunAjaran::create(['nama' => '2025/2026', 'semester' => 'Ganjil', 'is_aktif' => true]);
    }
}
