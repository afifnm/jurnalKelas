<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SekolahSeeder::class,
            TahunAjaranSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            KelasSeeder::class,
            MapelSeeder::class,
            JamPelajaranSeeder::class,
            JadwalSeeder::class,
            TugasMengajarSeeder::class,
        ]);
    }
}
