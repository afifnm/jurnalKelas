<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        Sekolah::create([
            'nama'           => 'SMK Pembangunan Nasional Sukoharjo',
            'nama_yayasan'   => 'Yayasan Bina Praja',
            'npsn'           => '20512345',
            'alamat'         => 'Sawah, Bulakrejo, Kec. Sukoharjo, Kabupaten Sukoharjo, Jawa Tengah 57551',
            'kepala_sekolah' => 'Marsono,S.Kom',
            'telepon'        => '(0271)592863',
            'email'          => 'mail@smkpemnasskh.sch.id',
            'website'        => 'www.smkpemnasskh.sch.id',
        ]);
    }
}
