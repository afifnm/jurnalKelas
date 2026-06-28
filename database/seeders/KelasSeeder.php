<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tingkat = ['X', 'XI', 'XII'];
        $jurusan = ['MA', 'MB', 'MC', 'TA', 'TB', 'TC', 'OA', 'OB', 'OC', 'RA', 'RB', 'RC'];
        
        foreach ($tingkat as $t) {
            foreach ($jurusan as $j) {
                Kelas::create(['nama' => "$t $j"]);
            }
        }
    }
}
