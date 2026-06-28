<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class JadwalSeeder extends Seeder
{
    /**
     * Tugas mengajar sesuai PDF "Pembagian Tugas Mengajar Guru Semester Gasal
     * SMK Negeri 2 Karanganyar TA 2026/2027".
     *
     * Format: kode guru (username) => [ [kode_mapel, [nama_kelas, ...]], ... ]
     * Tingkat: blok E/X => "X", F/XI => "XI", XII => "XII".
     * Jurusan (sub-kolom): MA MB MC TA TB TC OA OB OC RA RB RC.
     */
    private function tugas(): array
    {
        return [
            // ====== Halaman 1 : Adaptif / Normatif ======
            '101' => [['A1', ['X MA','X TA','X OA','X RA','XI MA','XI TA','XI OA','XI RA','XII MA','XII TA','XII OA']]],
            '102' => [['A1', ['X MB','X TB','X OB','X RB','XI MB','XI TB','XI OB','XI RB','XII MB','XII TB','XII OB']]],
            '103' => [['A1', ['X MC','X TC','X OC','X RC','XI MC','XI TC','XI OC','XI RC','XII MC','XII TC','XII OC','XII RC']]],
            '104' => [['A1', ['XI MA','XI RB','XII MA','XII TB','XII OB','XII RB','XII RC']]],
            '105' => [['A1', ['XI RB']]],

            '106' => [['A2', ['X MA','X MB','X MC','X TA','X TB','X TC','X OA','X OB','X OC','X RA','X RB','X RC','XI MA','XI MB','XI MC','XI TA','XI TB','XI TC']]],
            '107' => [['A2', ['XI OA','XI OB','XI OC','XI RA','XI RB','XI RC','XII MA','XII MB','XII MC','XII TA','XII TB','XII TC','XII OA','XII OB','XII OC','XII RA','XII RB','XII RC']]],

            '108' => [['A3', ['XI MA','XI MB','XI MC','XI TA','XII MA','XII MB','XII MC','XII TA']]],
            '109' => [['A3', ['XI TA','XI TB','XI TC','XII TB','XII TC','XII OA','XII OB','XII OC']]],
            '110' => [['A3', ['XII OA','XII OB','XII OC','XII RA','XII RB','XII RC']]],
            '111' => [
                ['A3', ['X TA','X TB','X TC','XI MA','XI MB','XI MC']],
                ['A5', ['X OA','X OB','X OC']],
            ],
            '112' => [
                ['A3', ['X MA','X MB','X MC','XI TA','XI TB','XI TC']],
                ['A5', ['X MA','X MB']],
            ],
            '113' => [['A5', ['X TA','X TB','X TC','X RA','X RB','X RC','XI MA','XI MB','XI MC','XI TA','XI TB','XI TC','XI OA','XI OB','XI OC','XI RA','XI RB','XI RC']]],
            '114' => [['A6', ['X MA','X MB','X MC','X TA','X TB','X TC','X OA','X OB','X OC','X RA','X RB','X RC']]],
            '115' => [
                ['A4', ['XI MA','XI MB','XI MC','XI TA','XI TB','XI TC','XII MA','XII MB','XII MC']],
            ],

            // ====== Halaman 2 ======
            '116' => [['A4', ['X RA','X RB','X RC','XI OA','XI OB','XI OC']]],
            '117' => [['ML', ['X TA','X TB','X TC','XII MA','XII MB','XII OA','XII OB']]],
            '118' => [['ML', ['XI MA','XI MB','XI MC','XI TA','XI TB','XII RA','XII RB','XII RC']]],
            '119' => [['ML', ['X MA','X MB','X MC','XI MA','XI MB','XI MC','XI TA','XI TB','XII MA','XII MB','XII MC','XII TA','XII RA']]],
            '120' => [['BK', ['XI MA','XI MB','XI MC','XII MA','XII MB','XII MC','XII RA']]],
            '121' => [['BK', ['XI TA','XI TB','XI TC','XII TA','XII TB','XII TC','XII RB']]],
            '122' => [['BK', ['XI OA','XI OB','XI OC','XII OA','XII OB','XII OC','XII RC']]],

            // ====== Halaman 2 : Jurusan Mesin (M) ======
            '201' => [['B1', ['X MA','X MB','X MC','XI MA','XI MB','XI MC','XII MA','XII MB']]],
            '202' => [['B1', ['XI MA']]],
            '203' => [['B2', ['XI MA','XI MB','XI MC']]],
            '204' => [['B2', ['X MA','X MB','X MC','XII MA','XII MB','XII MC']]],
            '205' => [['B4', ['X MA','X MB','X MC','X TA']]],
            '206' => [['B7-M', ['XII MA','XII MB','XII MC','XI MA']]],
            '207' => [
                ['B9-M1', ['XI MA','XI MB','XI MC']],
                ['M4', ['XII MA','XII MB','XII MC']],
            ],
            '208' => [['M1', ['X MA','X MB']]],

            // ====== Halaman 3 : Mesin lanjutan ======
            '209' => [['M8', ['XII MA','XII MB']]],
            '210' => [['M8', ['XI MA','XI MB']]],
            '211' => [
                ['M5', ['XI MA','XII MA']],
                ['B9-M2', ['XII MA']],
                ['M6', ['XI MA']],
            ],
            '212' => [
                ['M5', ['XII MA']],
                ['B9-M2', ['XII MB']],
                ['M6', ['XII MC']],
            ],
            '213' => [
                ['M5', ['XII MB']],
                ['B9-M2', ['XII MC']],
                ['M6', ['XII MA']],
            ],
            '214' => [
                ['M5', ['XI MB']],
                ['M6', ['XI MB']],
            ],
            '215' => [
                ['B7-M', ['XI MB','XI MC']],
                ['M2', ['X MA','X MB','X MC']],
                ['M7', ['XII MA','XII MB','XII MC']],
            ],
            '216' => [
                ['M8', ['XI MA']],
                ['M6', ['XI MA']],
                ['M5', ['XI MA']],
                ['BK', ['XI MA','XI MB','XI MC']],
            ],
            '217' => [
                ['M1', ['X MC']],
                ['M3', ['X MA','X MB','X MC']],
                ['C', ['X MA','X MB','X MC']],
                ['A4', ['X MA','X MB','X MC']],
            ],

            // ====== Halaman 4 : Jurusan Tekstil (T) ======
            '301' => [
                ['B1', ['X TA','X TB','X TC','XI TA','XI TB','XI TC','XII TA']],
                ['A4', ['X TA','X TB','X TC']],
            ],
            '302' => [['B2', ['X TA','X TB','X TC','XI TA','XI TB','XI TC']]],
            '303' => [
                ['T3', ['XI TA','XI TB','XI TC']],
                ['B9-T2', ['XII TA','XII TB','XII TC']],
            ],
            '304' => [
                ['B5', ['X TA','X TB']],
                ['T5', ['XII TA']],
                ['T2', ['XI TA','XI TB','XI TC']],
            ],
            '305' => [
                ['T3', ['XII TA','XII TB','XII TC']],
                ['T4', ['XII TA','XII TB','XII TC']],
            ],
            '306' => [
                ['T5', ['XII TA','XII TB']],
                ['T2', ['XII TC']],
            ],
            '307' => [
                ['T1', ['XI TA','XI TB','XI TC']],
                ['T2', ['XII TA','XII TB']],
                ['B9-T1', ['XI TA','XI TB']],
            ],
            '308' => [
                ['B5', ['X TC']],
                ['B9-T1', ['XI TA']],
                ['B7-T', ['XI TA','XI TB','XI TC']],
            ],
            'MRZ' => [['B7-T', ['XII TA','XII TB','XII TC']]],

            // ====== Halaman 4-5 : Jurusan Otomotif (O) ======
            '401' => [['B1', ['X OA','X OB','X OC','XI OA','XI OB','XI OC','XII OA']]],
            '402' => [['B2', ['X OA','X OB','X OC','XII OA','XII OB','XII OC']]],
            '403' => [['B4', ['X OA','X OB','X OC']]],
            '404' => [
                ['O2', ['X OA','X OB','X OC']],
                ['O6', ['XII OA','XII OB','XII OC']],
            ],
            '405' => [
                ['O7', ['XI OA','XI OB','XI OC']],
                ['O5', ['XII OA','XII OB','XII OC']],
                ['BK', ['XI OA','XI OB','XI OC']],
            ],

            // ====== Halaman 5 ======
            '406' => [
                ['O4', ['XII OA','XII OB','XII OC']],
                ['B9-O1', ['XI OA','XI OB','XI OC']],
            ],
            '407' => [['O5', ['XI OA','XI OB','XI OC']]],
            '408' => [['O3', ['X OA','X OB','X OC']]],
            '409' => [
                ['O1', ['X OA','X OB','X OC']],
                ['O5', ['XII OA','XII OB','XII OC']],
            ],
            '410' => [
                ['B9-O2', ['XI OA','XI OB','XI OC']],
                ['O6', ['XII OA','XII OB']],
            ],
            '411' => [['B7-O', ['XI OA','XI OB','XII OA','XII OB','XII OC']]],
            '412' => [['O2', ['XI OA','XI OB']]],

            // ====== Halaman 5-7 : Jurusan RPL/PPLG (R) ======
            '501' => [['B1', ['X RA','X RB','X RC','XI RA','XI RB','XI RC','XII RA']]],
            '502' => [['B2', ['X RA','X RB','X RC','XII RA','XII RB','XII RC']]],
            '503' => [['B2', ['XI RA','XI RB','XI RC','XII RA','XII RB','XII RC']]],
            '504' => [['B7', ['X RA','XI RA','XI RB','XI RC']]],

            // ====== Halaman 6 ======
            '505' => [
                ['R1', ['X RA','X RB','X RC']],
                ['R6', ['XII RA','XII RB','XII RC']],
            ],
            '506' => [['B9-R2', ['XII RA','XII RB','XII RC']]],
            '507' => [
                ['B7-R', ['XI RA','XI RB','XII RC']],
                ['BK', ['XI RA','XI RB','XI RC']],
            ],
            '508' => [['R4', ['XI RA','XI RB','XI RC','XII RB','XII RC']]],
            '509' => [['R5', ['XI RA','XI RB','XI RC','XII RA','XII RB','XII RC']]],
            '510' => [
                ['R2', ['XI RA','XI RB','XI RC']],
                ['BK', ['XI RA','XI RB','XI RC']],
            ],
            '511' => [
                ['B3', ['X RA','X RB','X RC']],
                ['C', ['X RA','X RB','X RC']],
                ['B7-O', ['XI RA']],
            ],
            '512' => [
                ['B3', ['X TA','X TB']],
                ['R3', ['XII RA','XII RB','XII RC']],
                ['BK', ['X OA','X OB']],
            ],
            '513' => [
                ['B3', ['X OA','X OB','X OC']],
                ['C', ['X OA','X OB']],
                ['R6', ['XII RA']],
                ['R3', ['XII RA']],
                ['BK', ['X RA','X RB']],
            ],
            '514' => [
                ['B3', ['X RB']],
                ['R6', ['XII RB','XII RC']],
                ['R3', ['XII RB','XII RC']],
            ],

            // ====== Halaman 7 ======
            '515' => [
                ['B3', ['X RC']],
                ['R4', ['XII RA']],
                ['B9-R1', ['XI RA','XI RB','XI RC']],
            ],
        ];
    }

    public function run(): void
    {
        $ta = TahunAjaran::where('is_aktif', true)->first();
        if (!$ta) {
            Log::warning('JadwalSeeder: tidak ada tahun ajaran aktif, dilewati.');
            return;
        }

        $jam = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get();
        if ($jam->isEmpty()) {
            Log::warning('JadwalSeeder: tidak ada jam pelajaran, dilewati.');
            return;
        }

        // Cache lookup
        $guruByKode  = User::pluck('id', 'username');
        $mapelByKode = Mapel::pluck('id', 'kode');
        $kelasByNama = Kelas::pluck('id', 'nama');

        $guruSchedule  = [];
        $kelasSchedule = [];
        $mapelKelas    = [];
        $missing       = [];

        foreach ($this->tugas() as $kodeGuru => $items) {
            $guruId = $guruByKode[$kodeGuru] ?? null;
            if (!$guruId) {
                $missing[] = "guru:$kodeGuru";
                continue;
            }

            foreach ($items as [$mapelKode, $kelasList]) {
                $mapelId = $mapelByKode[$mapelKode] ?? null;
                if (!$mapelId) {
                    $missing[] = "mapel:$mapelKode";
                    continue;
                }

                foreach ($kelasList as $kelasNama) {
                    $kelasId = $kelasByNama[$kelasNama] ?? null;
                    if (!$kelasId) {
                        $missing[] = "kelas:$kelasNama";
                        continue;
                    }

                    // Cegah mapel yang sama diajar 2x di kelas yang sama
                    if (isset($mapelKelas[$mapelId][$kelasId])) {
                        continue;
                    }

                    // Cari slot pertama yang bebas untuk guru & kelas ini
                    $slot = $jam->first(function ($j) use ($guruSchedule, $kelasSchedule, $guruId, $kelasId) {
                        return empty($guruSchedule[$guruId][$j->id])
                            && empty($kelasSchedule[$kelasId][$j->id]);
                    });

                    if (!$slot) {
                        continue; // pool slot habis (praktis tak terjadi)
                    }

                    Jadwal::create([
                        'guru_id'          => $guruId,
                        'kelas_id'         => $kelasId,
                        'mapel_id'         => $mapelId,
                        'tahun_ajaran_id'  => $ta->id,
                        'jam_pelajaran_id' => $slot->id,
                    ]);

                    $guruSchedule[$guruId][$slot->id]   = true;
                    $kelasSchedule[$kelasId][$slot->id] = true;
                    $mapelKelas[$mapelId][$kelasId]     = true;
                }
            }
        }

        if (!empty($missing)) {
            Log::warning('JadwalSeeder: referensi tidak ditemukan: ' . implode(', ', array_unique($missing)));
        }
    }
}
