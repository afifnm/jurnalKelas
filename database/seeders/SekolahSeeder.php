<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        Sekolah::create([
            'nama'           => 'SMK Nusantara Bangsa',
            'nama_yayasan'   => 'Yayasan Pendidikan Nusantara',
            'npsn'           => '20512345',
            'alamat'         => 'Jl. Pendidikan No. 1, Kota Bandung, Jawa Barat 40111',
            'kepala_sekolah' => 'Dr. Ahmad Santoso, M.Pd.',
            'telepon'        => '022-1234567',
            'email'          => 'info@smknusantara.sch.id',
            'website'        => 'www.smknusantara.sch.id',
        ]);
    }
}
