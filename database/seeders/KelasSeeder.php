<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = ['RPL', 'TKJ', 'AKL', 'OTKP'];
        $tingkat = ['X', 'XI', 'XII'];
        
        foreach ($tingkat as $t) {
            foreach ($jurusan as $j) {
                for ($i = 1; $i <= 2; $i++) {
                    Kelas::create(['nama' => "$t $j $i"]);
                }
            }
        }
    }
}
