<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'nama'      => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@sekolah.id',
            'password'  => Hash::make('admin123'),
            'no_hp'     => '08100000001',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
    }
}
