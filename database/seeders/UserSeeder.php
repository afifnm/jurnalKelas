<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        $admin = User::create([
            'nama'      => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@sekolah.id',
            'password'  => Hash::make('admin123'),
            'no_hp'     => '08100000001',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $ks = User::create([
            'nama'      => 'Dr. Ahmad Santoso, M.Pd.',
            'username'  => 'kepsek',
            'email'     => 'kepsek@sekolah.id',
            'password'  => Hash::make('kepsek123'),
            'no_hp'     => '08100000002',
            'is_active' => true,
        ]);
        $ks->assignRole('ks');

        $guruData = [
            ['nama' => 'Budi Hartono, S.Pd.',     'username' => 'guru_budi',    'email' => 'budi@sekolah.id'],
            ['nama' => 'Siti Rahayu, S.Pd.',      'username' => 'guru_siti',    'email' => 'siti@sekolah.id'],
        ];

        foreach ($guruData as $i => $data) {
            $guru = User::create(array_merge($data, [
                'password'  => Hash::make('guru123'),
                'no_hp'     => '0810000000' . ($i + 3),
                'is_active' => true,
            ]));
            $guru->assignRole('guru');
        }

        for ($i = 0; $i < 20; $i++) {
            $nama = $faker->name . ', ' . $faker->randomElement(['S.Pd.', 'S.Kom.', 'S.Si.', 'M.Pd.', 'S.Ag.']);
            $guru = User::create([
                'nama'      => $nama,
                'username'  => 'guru_' . strtolower(str_replace(' ', '', preg_replace('/[^A-Za-z0-9 ]/', '', $faker->firstName . rand(10,99)))),
                'email'     => $faker->unique()->safeEmail,
                'password'  => Hash::make('guru123'),
                'no_hp'     => $faker->phoneNumber,
                'is_active' => true,
            ]);
            $guru->assignRole('guru');
        }
    }
}
