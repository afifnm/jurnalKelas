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
        $jadwals = Jadwal::with(['mapel', 'jamPelajaran'])->get();

        $startDate = Carbon::today()->subMonths(2);
        $endDate   = Carbon::today();

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
            'Pendalaman materi dengan diskusi interaktif',
        ];

        while ($startDate->lte($endDate)) {
            $hari = $startDate->dayOfWeekIso;

            if ($hari <= 5) {
                $jadwalsToday = $jadwals->filter(fn($j) => $j->jamPelajaran?->hari === $hari);

                foreach ($jadwalsToday as $jadwal) {
                    if (rand(1, 100) > 85) continue;

                    $jamMulai = $jadwal->jamPelajaran->jam_mulai;
                    // Waktu input acak: mulai dari jam mulai s.d. 20 menit sesudahnya
                    $createdAt = $startDate->copy()
                        ->setTimeFromTimeString($jamMulai)
                        ->addMinutes(rand(0, 20));

                    $materi = $faker->randomElement($materiList) . ' - ' . $jadwal->mapel->nama;

                    $jurnal = Jurnal::make([
                        'jadwal_id'       => $jadwal->id,
                        'guru_id'         => $jadwal->guru_id,
                        'kelas_id'        => $jadwal->kelas_id,
                        'mapel_id'        => $jadwal->mapel_id,
                        'tahun_ajaran_id' => $jadwal->tahun_ajaran_id,
                        'tanggal'         => $startDate->format('Y-m-d'),
                        'materi'          => $materi,
                        'catatan'         => null,
                    ]);

                    $jurnal->created_at = $createdAt;
                    $jurnal->updated_at = $createdAt;
                    $jurnal->save();
                }
            }

            $startDate->addDay();
        }
    }
}
