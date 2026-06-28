<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::create(['nama' => '2026/2027', 'semester' => 'Ganjil', 'is_aktif' => true]);
        TahunAjaran::create(['nama' => '2026/2027', 'semester' => 'Genap', 'is_aktif' => false]);
        TahunAjaran::create(['nama' => '2027/2028', 'semester' => 'Ganjil', 'is_aktif' => false]);
    }
}
