<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            ['nama' => 'Matematika',             'kode' => 'MTK'],
            ['nama' => 'Bahasa Indonesia',       'kode' => 'BIND'],
            ['nama' => 'Bahasa Inggris',         'kode' => 'BING'],
            ['nama' => 'Ilmu Pengetahuan Alam',  'kode' => 'IPA'],
            ['nama' => 'Ilmu Pengetahuan Sosial','kode' => 'IPS'],
            ['nama' => 'Pemrograman Dasar',      'kode' => 'PRGD'],
            ['nama' => 'Jaringan Komputer',      'kode' => 'JKP'],
            ['nama' => 'Basis Data',             'kode' => 'BD'],
            ['nama' => 'Pendidikan Agama Islam', 'kode' => 'PAI'],
            ['nama' => 'Pendidikan Pancasila',   'kode' => 'PKN'],
        ];

        foreach ($mapel as $m) {
            Mapel::create($m);
        }
    }
}
