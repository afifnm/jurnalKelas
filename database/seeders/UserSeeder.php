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

        $guruData = [
            ['nama' => 'Hd. Istiqomah, S.Ag', 'username' => '101'],
            ['nama' => 'Sutrisno, S.Pd.I., M.Pd.', 'username' => '102'],
            ['nama' => 'Agus Rumahno, S.PdI.', 'username' => '103'],
            ['nama' => 'Daniel Kristanto, S.Th.', 'username' => '104'],
            ['nama' => 'Jari Purwanto, S.Pd.H.', 'username' => '105'],
            ['nama' => 'Drs. Suparno', 'username' => '106'],
            ['nama' => 'Sunarno, S.Pd., M.Pd.', 'username' => '107'],
            ['nama' => 'Nining Hartati, S.Pd', 'username' => '108'],
            ['nama' => 'Widi Santosa, S.Pd., M.Pd', 'username' => '109'],
            ['nama' => 'Joko Sriyanto, S.Pd', 'username' => '110'],
            ['nama' => 'Ima Aryani Inprasetyawati, S.Pd', 'username' => '111'],
            ['nama' => 'Alvi Masruri, S.Pd.', 'username' => '112'],
            ['nama' => 'Junnatun Muyasiroh, S.Pd', 'username' => '113'],
            ['nama' => 'Astitik, S.Sn, M.Pd.', 'username' => '114'],
            ['nama' => 'Purwanta, S.Pd', 'username' => '115'],
            ['nama' => 'Sukirno, S.Pd', 'username' => '116'],
            ['nama' => 'Danang Cahyo Binarto, S.Pd.', 'username' => '117'],
            ['nama' => 'Rahayu Nur Istiana, S.Pd., M.Pd', 'username' => '118'],
            ['nama' => 'Jungkung Murtiyoso, S.S', 'username' => '119'],
            ['nama' => 'Endah Dwi Sayekti, S.Psi, M.Si', 'username' => '120'],
            ['nama' => 'Puput Sinta Dewi, S.Psi.', 'username' => '121'],
            ['nama' => 'Fitriyah Maimun Thofiah, S.Pd.', 'username' => '122'],
            ['nama' => 'Dra. Sri Bidayatiningsih', 'username' => '201'],
            ['nama' => 'Asikotul Choiroh, S.Pd.', 'username' => '202'],
            ['nama' => 'Sri Esti Sulistyantini, S.Pd., M.Pd', 'username' => '203'],
            ['nama' => 'Ismi Puji Hastuti, S.Pd.', 'username' => '204'],
            ['nama' => 'Herdi Munandar, S.Pd', 'username' => '205'],
            ['nama' => 'Saptono, S.Pd., M.Pd', 'username' => '206'],
            ['nama' => 'Nukis, S.Pd., M.Eng', 'username' => '207'],
            ['nama' => 'Sri Mulyono, S.Pd', 'username' => '208'],
            ['nama' => 'Cahyono, S.Pd., M.Eng', 'username' => '209'],
            ['nama' => 'Muchamad Daim, S.Pd', 'username' => '210'],
            ['nama' => 'Sutarman, S.Pd', 'username' => '211'],
            ['nama' => 'Sukasno, S.Pd', 'username' => '212'],
            ['nama' => 'Heri Susanto, S.Pd', 'username' => '213'],
            ['nama' => 'Agus Darmadi, S.Pd.', 'username' => '214'],
            ['nama' => 'Heri Setyo Basuki, S.T', 'username' => '214B'],
            ['nama' => 'Suwardi, S.Pd', 'username' => '215'],
            ['nama' => 'Petrus Hary Setiawan, S.T.', 'username' => '216'],
            ['nama' => 'Dedi Kurniawan, S.Pd.', 'username' => '217'],
            ['nama' => 'Darsono, S.Pd.', 'username' => '301'],
            ['nama' => 'Jinahwi Pertiwiningsih, S.Pd', 'username' => '302'],
            ['nama' => 'Fuadiyah Arief, ST', 'username' => '303'],
            ['nama' => 'Fanani Artiningsih, ST', 'username' => '304'],
            ['nama' => 'Untung Mardiyanto, ST', 'username' => '305'],
            ['nama' => 'Kusrini Yuliani, ST', 'username' => '306'],
            ['nama' => 'Siti Solechah, S.T.', 'username' => '307'],
            ['nama' => 'Dian Ekawati, S.T.', 'username' => '308'],
            ['nama' => 'MR Z', 'username' => 'MRZ'],
            ['nama' => 'Rini Kusumawati, S.Si', 'username' => '401'],
            ['nama' => 'Wesli Ari Setyo Hutomo, S. Pd', 'username' => '402'],
            ['nama' => 'Sri Wahyuni, S.Pd', 'username' => '403'],
            ['nama' => 'Supadmo, S.Pd', 'username' => '404'],
            ['nama' => 'Isnu Pujio, S.T.', 'username' => '405'],
            ['nama' => 'Linda Yusianita Happy, S.T.', 'username' => '406'],
            ['nama' => 'Mochamad Taufik, S.Pd.', 'username' => '407'],
            ['nama' => 'Mulyono, S.Pd.', 'username' => '408'],
            ['nama' => 'Rochmat Hidayat, S.T.', 'username' => '409'],
            ['nama' => 'Rahmat Muttaqin, S.T.', 'username' => '410'],
            ['nama' => 'D. Sri Herni SS, S.Pd', 'username' => '411'],
            ['nama' => 'Harnanto Prubinantoro, S.T', 'username' => '412'],
            ['nama' => 'Pangesti Arum P., S.Pd., M.Pd.', 'username' => '501'],
            ['nama' => 'Musti Rahayu, S.Pd', 'username' => '502'],
            ['nama' => 'Panti Candra Sari, SS', 'username' => '503'],
            ['nama' => 'Dwi Sri Wahyuni, S.Pd', 'username' => '504'],
            ['nama' => 'Arief Kurniawan, ST.', 'username' => '505'],
            ['nama' => 'Budi Sulistiyo, S.Kom., M.Kom.', 'username' => '506'],
            ['nama' => 'Atik Retnoningsih, S.Kom', 'username' => '507'],
            ['nama' => 'Dwi Nuryani S.Kom', 'username' => '508'],
            ['nama' => 'Agung Wiratmo, S.Pd.', 'username' => '509'],
            ['nama' => 'Carolin Windiasri, S.Pd.', 'username' => '510'],
            ['nama' => 'Teguh Priyanto, S.Pd.T.', 'username' => '511'],
            ['nama' => 'Tri Ani Sulistyo Wardani, S.Kom', 'username' => '512'],
            ['nama' => 'Tina Fajrin, S.Pd. S. Kom.', 'username' => '513'],
            ['nama' => 'Liana Masitoh, S.Kom', 'username' => '514'],
            ['nama' => 'Afif Nuruddin Maisaroh, S.Pd.', 'username' => '515'],
        ];

        foreach ($guruData as $i => $data) {
            $guru = User::create(array_merge($data, [
                'password'  => Hash::make('12345678'),
                'no_hp'     => '08100' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]));
            $guru->assignRole('guru');
        }
    }
}

