<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Jurnal;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class JurnalSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $jadwals = Jadwal::with('mapel')->get();
        
        $startDate = Carbon::today()->subMonths(2);
        $endDate = Carbon::today();
        
        $materiList = [
            'Pendahuluan dan pengenalan materi',
            'Pembahasan bab 1: Konsep dasar',
            'Latihan soal dan diskusi kelompok',
            'Presentasi tugas siswa',
            'Review materi minggu lalu dan kuis',
            'Materi lanjutan dan studi kasus',
            'Praktik lapangan / laboratorium',
            'Evaluasi tengah semester',
            'Membahas PR dan tugas individu',
            'Simulasi dan praktek',
            'Pendalaman materi dengan diskusi interaktif'
        ];

        $metodeList = ['Ceramah', 'Diskusi', 'Tanya Jawab', 'Demonstrasi', 'Eksperimen', 'Presentasi', 'Kerja Kelompok', 'Proyek'];
        $kendalaList = [null, null, null, 'Beberapa siswa kurang fokus', 'Proyektor rusak', 'Listrik sempat padam', 'Siswa mengantuk', 'Ada acara sekolah sehingga waktu terpotong'];

        while ($startDate->lte($endDate)) {
            $hari = $startDate->dayOfWeekIso;
            
            if ($hari <= 5) { // Senin - Jumat
                $jadwalsToday = $jadwals->where('hari', $hari);
                
                foreach ($jadwalsToday as $jadwal) {
                    // 85% chance guru mengisi jurnal
                    if (rand(1, 100) <= 85) {
                        
                        $isTerlambat = rand(1, 100) <= 20; // 20% chance terlambat
                        $menitTerlambat = $isTerlambat ? rand(5, 30) : 0;
                        
                        $jamMasukAktual = Carbon::parse($jadwal->jam_mulai)->addMinutes($menitTerlambat)->format('H:i:s');
                        $jamKeluarAktual = Carbon::parse($jadwal->jam_selesai)->addMinutes(rand(-5, 5))->format('H:i:s');
                        
                        $materi = $faker->randomElement($materiList) . ' - ' . $jadwal->mapel->nama;
                        $metode = $faker->randomElement($metodeList);
                        $kendala = $faker->randomElement($kendalaList);
                        
                        Jurnal::create([
                            'jadwal_id'           => $jadwal->id,
                            'guru_id'             => $jadwal->guru_id,
                            'kelas_id'            => $jadwal->kelas_id,
                            'mapel_id'            => $jadwal->mapel_id,
                            'tahun_ajaran_id'     => $jadwal->tahun_ajaran_id,
                            'tanggal'             => $startDate->format('Y-m-d'),
                            'jam_masuk_aktual'    => $jamMasukAktual,
                            'jam_keluar_aktual'   => $jamKeluarAktual,
                            'materi'              => $materi,
                            'metode_pembelajaran' => $metode,
                            'kendala'             => $kendala,
                            'tindak_lanjut'       => $kendala ? 'Telah ditangani' : null,
                            'catatan'             => null,
                            'status'              => 'draft',
                            'is_terlambat'        => $isTerlambat,
                            'menit_terlambat'     => $menitTerlambat,
                        ]);
                    }
                }
            }
            $startDate->addDay();
        }
    }
}
