<?php

namespace Database\Seeders;

use App\Models\JamPelajaran;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JamPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        // 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat
        for ($hari = 1; $hari <= 5; $hari++) {
            $startTime = Carbon::createFromTime(7, 0, 0); // Mulai 07:00
            
            for ($jamKe = 1; $jamKe <= 10; $jamKe++) {
                $endTime = (clone $startTime)->addMinutes(45);
                
                $data[] = [
                    'hari'        => $hari,
                    'jam_ke'      => $jamKe,
                    'jam_mulai'   => $startTime->format('H:i'),
                    'jam_selesai' => $endTime->format('H:i'),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
                
                $startTime = clone $endTime;
                
                // Istirahat 1: 15 Menit setelah jam ke-4
                if ($jamKe == 4) {
                    $startTime->addMinutes(15);
                }
                
                // Istirahat 2 (ISHOMA): 45 Menit setelah jam ke-6
                if ($jamKe == 6) {
                    $startTime->addMinutes(45);
                }
            }
        }

        JamPelajaran::insert($data);
    }
}
