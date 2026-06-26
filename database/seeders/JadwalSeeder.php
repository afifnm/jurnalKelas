<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $ta       = TahunAjaran::where('is_aktif', true)->first();
        $guru     = User::role('guru')->get();
        $kelas    = Kelas::all();
        $mapel    = Mapel::all();

        $slots = [
            ['jam_mulai' => '07:00', 'jam_selesai' => '08:30'],
            ['jam_mulai' => '08:30', 'jam_selesai' => '10:00'],
            ['jam_mulai' => '10:15', 'jam_selesai' => '11:45'],
            ['jam_mulai' => '12:30', 'jam_selesai' => '14:00'],
            ['jam_mulai' => '14:15', 'jam_selesai' => '15:45'],
        ];

        $guruSchedule = [];
        $kelasSchedule = [];

        foreach ($guru as $g) {
            $numClasses = rand(5, 12);
            for ($i = 0; $i < $numClasses; $i++) {
                $hari = rand(1, 5); 
                $slotIdx = array_rand($slots);
                
                $k = $kelas->random();
                $m = $mapel->random();

                if (!isset($guruSchedule[$g->id][$hari][$slotIdx]) && !isset($kelasSchedule[$k->id][$hari][$slotIdx])) {
                    
                    Jadwal::create([
                        'guru_id'        => $g->id,
                        'kelas_id'       => $k->id,
                        'mapel_id'       => $m->id,
                        'tahun_ajaran_id'=> $ta->id,
                        'hari'           => $hari,
                        'jam_mulai'      => $slots[$slotIdx]['jam_mulai'],
                        'jam_selesai'    => $slots[$slotIdx]['jam_selesai'],
                    ]);

                    $guruSchedule[$g->id][$hari][$slotIdx] = true;
                    $kelasSchedule[$k->id][$hari][$slotIdx] = true;
                }
            }
        }
    }
}
