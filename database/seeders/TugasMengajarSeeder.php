<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TugasMengajarSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first()
            ?? TahunAjaran::first();

        $mapels   = Mapel::pluck('id', 'kode')->toArray();
        $gurus    = User::role('guru')->pluck('id', 'username')->toArray();
        $kelasMap = Kelas::pluck('id', 'nama')->toArray();

        /*
         * Data PERSIS dari CSV "draft-MENGAJAR GURU 2026 2027" (verified cell-identical).
         * Format: 'kode_guru' => [ 'kode_mapel' => ['Nama Kelas' => jp, ...], '_jabatan' => [['jam'=>n]] ]
         * Urutan kelas pada PDF: X/XI/XII x (MA MB MC TA TB TC OA OB OC RA RB RC).
         */
        $data = [
            '101' => [
                'A1' => ['X MA'=>3, 'X TA'=>3, 'X OA'=>3, 'X RA'=>3, 'XI MA'=>3, 'XI TA'=>3, 'XI OA'=>3, 'XI RA'=>3, 'XII MA'=>3, 'XII TA'=>3, 'XII OA'=>3, 'XII RA'=>3],
            ],
            '102' => [
                'A1' => ['X MB'=>3, 'X TB'=>3, 'X OB'=>3, 'X RB'=>3, 'XI MB'=>3, 'XI TB'=>3, 'XI OB'=>3, 'XI RB'=>3, 'XII MB'=>3, 'XII TB'=>3, 'XII OB'=>3, 'XII RB'=>3],
            ],
            '103' => [
                'A1' => ['X MC'=>3, 'X TC'=>3, 'X OC'=>3, 'X RC'=>3, 'XI MC'=>3, 'XI TC'=>3, 'XI OC'=>3, 'XI RC'=>3, 'XII MC'=>3, 'XII TC'=>3, 'XII OC'=>3, 'XII RC'=>3],
            ],
            '104' => [
                'A1' => ['XI MC'=>3, 'XI OC'=>3, 'XI RC'=>3, 'XII MA'=>3, 'XII TC'=>3, 'XII OC'=>3, 'XII RC'=>3],
            ],
            '105' => [
                'A1' => ['XI RC'=>3],
            ],
            '106' => [
                'A2' => ['X MA'=>2, 'X MB'=>2, 'X MC'=>2, 'X TA'=>2, 'X TB'=>2, 'X TC'=>2, 'X OA'=>2, 'X OB'=>2, 'X OC'=>2, 'X RA'=>2, 'X RB'=>2, 'X RC'=>2, 'XI MA'=>2, 'XI MB'=>2, 'XI MC'=>2, 'XI TA'=>2, 'XI TB'=>2, 'XI TC'=>2],
            ],
            '107' => [
                'A2' => ['XI OA'=>2, 'XI OB'=>2, 'XI OC'=>2, 'XI RA'=>2, 'XI RB'=>2, 'XI RC'=>2, 'XII MA'=>2, 'XII MB'=>2, 'XII MC'=>2, 'XII TA'=>2, 'XII TB'=>2, 'XII TC'=>2, 'XII OA'=>2, 'XII OB'=>2, 'XII OC'=>2, 'XII RA'=>2, 'XII RB'=>2, 'XII RC'=>2],
            ],
            '108' => [
                'A3' => ['XI MA'=>3, 'XI MB'=>3, 'XI MC'=>3, 'XI OA'=>3, 'XI OC'=>3, 'XII MA'=>3, 'XII MB'=>3, 'XII MC'=>3],
            ],
            '109' => [
                'A3' => ['XI TA'=>3, 'XI TB'=>3, 'XI TC'=>3, 'XII TA'=>3, 'XII TB'=>3, 'XII TC'=>3, 'XII RA'=>3, 'XII RB'=>3],
            ],
            '110' => [
                'A3' => ['X OA'=>4, 'X OB'=>4, 'X OC'=>4, 'XII OA'=>3, 'XII OB'=>3, 'XII OC'=>3, 'XII RC'=>3],
            ],
            '111' => [
                'A3' => ['X TA'=>4, 'X TB'=>4, 'X TC'=>4, 'X RA'=>4, 'X RB'=>4, 'X RC'=>4],
                'A5' => ['X OB'=>2, 'X RA'=>2, 'X RB'=>2],
            ],
            '112' => [
                'A3' => ['X MA'=>4, 'X MB'=>4, 'X MC'=>4, 'XI OB'=>3, 'XI RA'=>3, 'XI RB'=>3, 'XI RC'=>3],
                'A5' => ['X MA'=>2, 'X MB'=>2, 'X OA'=>2],
            ],
            '113' => [
                'A5' => ['X MC'=>2, 'X TA'=>2, 'X TB'=>2, 'X TC'=>2, 'X OC'=>2, 'X RC'=>2, 'XI MA'=>2, 'XI MB'=>2, 'XI MC'=>2, 'XI TA'=>2, 'XI TB'=>2, 'XI TC'=>2, 'XI OA'=>2, 'XI OB'=>2, 'XI OC'=>2, 'XI RA'=>2, 'XI RB'=>2, 'XI RC'=>2],
            ],
            '114' => [
                'A6' => ['X MA'=>2, 'X MB'=>2, 'X MC'=>2, 'X TA'=>2, 'X TB'=>2, 'X TC'=>2, 'X OA'=>2, 'X OB'=>2, 'X OC'=>2, 'X RA'=>2, 'X RB'=>2, 'X RC'=>2],
            ],
            '115' => [
                '_jabatan' => [['jam' => 12]],
                'A4' => ['XI MA'=>2, 'XI MB'=>2, 'XI MC'=>2, 'XI TA'=>2, 'XI TB'=>2, 'XI TC'=>2, 'XI RA'=>2, 'XI RB'=>2, 'XI RC'=>2],
            ],
            '116' => [
                'A4' => ['X RA'=>3, 'X RB'=>3, 'X RC'=>3, 'XI OA'=>2, 'XI OB'=>2, 'XI OC'=>2],
            ],
            '117' => [
                '_jabatan' => [['jam' => 12]],
                'ML' => ['X TA'=>2, 'X TB'=>2, 'X TC'=>2, 'XII TA'=>2, 'XII TB'=>2, 'XII TC'=>2, 'XII OB'=>2, 'XII OC'=>2],
            ],
            '118' => [
                'ML' => ['X OA'=>2, 'X OB'=>2, 'X OC'=>2, 'X RA'=>2, 'X RB'=>2, 'X RC'=>2, 'XI OB'=>2, 'XI OC'=>2, 'XI RA'=>2, 'XI RB'=>2, 'XI RC'=>2, 'XII RA'=>2, 'XII RB'=>2, 'XII RC'=>2],
            ],
            '119' => [
                '_jabatan' => [['jam' => 2]],
                'ML' => ['X MA'=>2, 'X MB'=>2, 'X MC'=>2, 'XI MA'=>2, 'XI MB'=>2, 'XI MC'=>2, 'XI TA'=>2, 'XI TB'=>2, 'XI TC'=>2, 'XI OA'=>2, 'XII MA'=>2, 'XII MB'=>2, 'XII MC'=>2, 'XII OA'=>2],
            ],
            '120' => [
                'BK' => ['X MC'=>5, 'X TC'=>5, 'X OC'=>5, 'XII MA'=>5, 'XII MB'=>5, 'XII MC'=>5, 'XII RA'=>5],
            ],
            '121' => [
                'BK' => ['X MC'=>5, 'X TC'=>5, 'X OC'=>5, 'XII OA'=>5, 'XII OB'=>5, 'XII OC'=>5, 'XII RB'=>5],
            ],
            '122' => [
                'BK' => ['X MC'=>5, 'X TC'=>5, 'X OC'=>5, 'XII TA'=>5, 'XII TB'=>5, 'XII TC'=>5, 'XII RC'=>5],
            ],
            '201' => [
                'B1' => ['XI MA'=>3, 'XI MB'=>3, 'XI TA'=>3, 'XI TB'=>3, 'XI OA'=>3, 'XI OB'=>3, 'XI RA'=>3, 'XI RB'=>3],
            ],
            '202' => [
                'B1' => ['X MA'=>4, 'X MB'=>4, 'X MC'=>4, 'XI MC'=>3, 'XII MA'=>3, 'XII MB'=>3, 'XII MC'=>3],
            ],
            '203' => [
                'B2' => ['XI MA'=>4, 'XI MB'=>4, 'XI MC'=>4, 'XI OA'=>4, 'XI OB'=>4, 'XI OC'=>4],
            ],
            '204' => [
                'B2' => ['X MA'=>4, 'X MB'=>4, 'X MC'=>4, 'XII MA'=>4, 'XII MB'=>4, 'XII MC'=>4],
            ],
            '205' => [
                'B4' => ['X MA'=>6, 'X MB'=>6, 'X MC'=>6, 'X TA'=>6],
            ],
            '206' => [
                'B7-M' => ['XI MC'=>5, 'XII MA'=>5, 'XII MB'=>5, 'XII MC'=>5],
            ],
            '207' => [
                'B9-M1' => ['XI MA'=>4, 'XI MB'=>4, 'XI MC'=>4],
                'M4' => ['XII MA'=>4, 'XII MB'=>4, 'XII MC'=>4],
            ],
            '208' => [
                '_jabatan' => [['jam' => 12]],
                'M1' => ['X MA'=>6, 'X MB'=>6],
                'A4' => ['X MA'=>3, 'X MB'=>3, 'X MC'=>3],
            ],
            '209' => [
                '_jabatan' => [['jam' => 12]],
                'M8' => ['XII MB'=>6, 'XII MC'=>6],
            ],
            '210' => [
                '_jabatan' => [['jam' => 12]],
                'M8' => ['XI MB'=>6, 'XI MC'=>6],
            ],
            '211' => [
                'M5' => ['XI MC'=>6, 'XII MA'=>3],
                'B9-M2' => ['XII MA'=>4],
                'M6' => ['XI MC'=>6, 'XII MA'=>5],
            ],
            '212' => [
                '_jabatan' => [['jam' => 12]],
                'M5' => ['XII MB'=>3],
                'B9-M2' => ['XII MB'=>4],
                'M6' => ['XII MB'=>5],
            ],
            '213' => [
                '_jabatan' => [['jam' => 12]],
                'M5' => ['XII MC'=>3],
                'B9-M2' => ['XII MC'=>4],
                'M6' => ['XII MC'=>5],
            ],
            '214' => [
                '_jabatan' => [['jam' => 12]],
                'M5' => ['XI MB'=>6],
                'M6' => ['XI MB'=>6],
            ],
            '215' => [
                'B7-M' => ['XI MA'=>5, 'XI MB'=>5],
                'M2' => ['X MA'=>2, 'X MB'=>2, 'X MC'=>2],
                'M7' => ['XII MA'=>2, 'XII MB'=>2, 'XII MC'=>2],
                'BK' => ['X MA'=>5, 'X MB'=>5],
            ],
            '216' => [
                'M8' => ['XI MA'=>6, 'XII MA'=>6],
                'M6' => ['XI MA'=>6],
                'M5' => ['XI MA'=>6],
                'BK' => ['XI MA'=>5, 'XI MB'=>5, 'XI MC'=>5],
            ],
            '217' => [
                'M1' => ['X MC'=>6],
                'M3' => ['X MA'=>4, 'X MB'=>4, 'X MC'=>4],
                'C' => ['X MA'=>2, 'X MB'=>2, 'X MC'=>2, 'X OA'=>3, 'X OB'=>3, 'X OC'=>3],
            ],
            '301' => [
                'B1' => ['X TA'=>4, 'X TB'=>4, 'X TC'=>4, 'XI RC'=>3, 'XII TA'=>3, 'XII TB'=>3, 'XII TC'=>3],
                'A4' => ['X TA'=>3, 'X TB'=>3, 'X TC'=>3],
            ],
            '302' => [
                '_jabatan' => [['jam' => 2]],
                'B2' => ['X TA'=>4, 'X TB'=>4, 'X TC'=>4, 'XI TA'=>4, 'XI TB'=>4, 'XI TC'=>4],
            ],
            '303' => [
                'T3' => ['XI TA'=>8, 'XI TB'=>8, 'XI TC'=>8],
                'B9-T2' => ['XII TA'=>4, 'XII TB'=>4, 'XII TC'=>4],
            ],
            '304' => [
                'B5' => ['X TA'=>12, 'X TB'=>12],
                'T5' => ['XII TC'=>6],
                'T2' => ['XI TA'=>2, 'XI TB'=>2, 'XI TC'=>2],
            ],
            '305' => [
                'T3' => ['XII TA'=>8, 'XII TB'=>8, 'XII TC'=>8],
                'T4' => ['XII TA'=>4, 'XII TB'=>4, 'XII TC'=>4],
            ],
            '306' => [
                '_jabatan' => [['jam' => 12]],
                'T5' => ['XII TA'=>6, 'XII TB'=>6],
                'T2' => ['XII TB'=>2],
            ],
            '307' => [
                'T1' => ['XI TA'=>8, 'XI TB'=>8, 'XI TC'=>8],
                'T2' => ['XII TA'=>2, 'XII TC'=>2],
                'B9-T1' => ['XI TB'=>4, 'XI TC'=>4],
            ],
            '308' => [
                'B5' => ['X TC'=>12],
                'B9-T1' => ['XI TA'=>4],
                'B7-T' => ['XI TA'=>5, 'XI TB'=>5, 'XI TC'=>5],
            ],
            'MRZ' => [
                'B7-T' => ['XII TA'=>5, 'XII TB'=>5, 'XII TC'=>5],
                'BK' => ['XI TA'=>5, 'XI TB'=>5, 'XI TC'=>5],
            ],
            '401' => [
                'B1' => ['X OA'=>4, 'X OB'=>4, 'X OC'=>4, 'XI OC'=>3, 'XII OA'=>3, 'XII OB'=>3, 'XII OC'=>3],
            ],
            '402' => [
                '_jabatan' => [['jam' => 2]],
                'B2' => ['X OA'=>4, 'X OB'=>4, 'X OC'=>4, 'XII OA'=>4, 'XII OB'=>4, 'XII OC'=>4],
            ],
            '403' => [
                'B4' => ['X TC'=>6, 'X OA'=>6, 'X OB'=>6, 'X OC'=>6],
            ],
            '404' => [
                'O2' => ['X OA'=>4, 'X OB'=>4, 'X OC'=>2],
                'O6' => ['XI OB'=>5, 'XI OC'=>5, 'XII OA'=>5],
            ],
            '405' => [
                'O7' => ['XI OA'=>3, 'XI OB'=>3, 'XI OC'=>3, 'XII OA'=>5, 'XII OB'=>5, 'XII OC'=>5],
                'BK' => ['XI OA'=>5, 'XI OB'=>5, 'XI OC'=>5],
            ],
            '406' => [
                'O4' => ['XII OA'=>5, 'XII OB'=>5, 'XII OC'=>5],
                'B9-O1' => ['XI OA'=>4, 'XI OB'=>4, 'XI OC'=>4],
            ],
            '407' => [
                '_jabatan' => [['jam' => 12]],
                'O5' => ['XI OA'=>5, 'XI OB'=>5, 'XI OC'=>5],
            ],
            '408' => [
                '_jabatan' => [['jam' => 12]],
                'O3' => ['X OA'=>4, 'X OB'=>4, 'X OC'=>4],
            ],
            '409' => [
                'O1' => ['X OA'=>2, 'X OB'=>4, 'X OC'=>4],
                'O5' => ['XII OA'=>5, 'XII OB'=>5, 'XII OC'=>5],
            ],
            '410' => [
                'B9-O2' => ['XII OA'=>4, 'XII OB'=>4, 'XII OC'=>4],
                'O6' => ['XI OA'=>5, 'XII OB'=>5, 'XII OC'=>5],
            ],
            '411' => [
                'B7-O' => ['XI OA'=>5, 'XI OB'=>5, 'XII OA'=>5, 'XII OB'=>5, 'XII OC'=>5],
            ],
            '412' => [
                'O2' => ['X OA'=>2, 'X OC'=>2],
                'O4' => ['XI OA'=>5, 'XI OB'=>5, 'XI OC'=>5],
                'B7-O' => ['XI OC'=>5],
            ],
            '501' => [
                'B1' => ['X RA'=>4, 'X RB'=>4, 'X RC'=>4, 'XI TC'=>3, 'XII RA'=>3, 'XII RB'=>3, 'XII RC'=>3],
            ],
            '502' => [
                'B2' => ['X RA'=>4, 'X RB'=>4, 'X RC'=>4, 'XII RA'=>4, 'XII RB'=>4, 'XII RC'=>4],
            ],
            '503' => [
                'B2' => ['XI RA'=>4, 'XI RB'=>4, 'XI RC'=>4, 'XII TA'=>4, 'XII TB'=>4, 'XII TC'=>4],
            ],
            '504' => [
                'B7' => ['X TB'=>6, 'X RA'=>6, 'X RB'=>6, 'X RC'=>6],
            ],
            '505' => [
                'R1' => ['X RA'=>4, 'X RB'=>4, 'X RC'=>4],
                'R6' => ['XII RA'=>4, 'XII RB'=>4, 'XII RC'=>4],
            ],
            '506' => [
                '_jabatan' => [['jam' => 12]],
                'B9-R2' => ['XII RA'=>4, 'XII RB'=>4, 'XII RC'=>4],
            ],
            '507' => [
                'B7-R' => ['XI RA'=>5, 'XI RB'=>5, 'XI RC'=>5, 'XII RB'=>5, 'XII RC'=>5],
                'BK' => ['XI RA'=>5, 'XI RB'=>5, 'XI RC'=>5],
            ],
            '508' => [
                'R4' => ['XI RA'=>6, 'XI RB'=>6, 'XI RC'=>6, 'XII RB'=>6, 'XII RC'=>6],
            ],
            '509' => [
                '_jabatan' => [['jam' => 12]],
                'R5' => ['XI RA'=>4, 'XI RB'=>4, 'XI RC'=>4, 'XII RA'=>4, 'XII RB'=>4, 'XII RC'=>4],
            ],
            '510' => [
                'R2' => ['X RA'=>8, 'X RB'=>8, 'X RC'=>8],
                'BK' => ['X RA'=>5, 'X RB'=>5, 'X RC'=>5],
            ],
            '511' => [
                'B3' => ['X MA'=>4, 'X MB'=>4, 'X MC'=>4],
                'C' => ['X TA'=>2, 'X TB'=>2, 'X TC'=>2, 'X OC'=>2],
                'B7-O' => ['XII RA'=>5],
            ],
            '512' => [
                'B3' => ['X TA'=>4, 'X TB'=>4],
                'R3' => ['XII RA'=>6, 'XII RB'=>6, 'XII RC'=>6],
                'BK' => ['X TA'=>5, 'X TB'=>5],
            ],
            '513' => [
                'B3' => ['X TA'=>4, 'X OA'=>4, 'X OB'=>4],
                'C' => ['X OA'=>2, 'X OB'=>2],
                'R6' => ['XI RA'=>4],
                'R3' => ['XI RA'=>4],
                'BK' => ['X OA'=>5, 'X OB'=>5],
            ],
            '514' => [
                'B3' => ['X OC'=>4, 'X RA'=>4],
                'C' => ['X RA'=>2],
                'R6' => ['XI RB'=>4, 'XI RC'=>4],
                'R3' => ['XI RB'=>4, 'XI RC'=>4],
            ],
            '515' => [
                'B3' => ['X RB'=>4, 'X RC'=>4],
                'R4' => ['X RB'=>2, 'X RC'=>2, 'XII RA'=>6],
                'B9-R1' => ['XI RA'=>4, 'XI RB'=>4, 'XI RC'=>4],
            ],
        ];

        DB::table('tugas_mengajar')->truncate();
        DB::table('guru_jabatan_khusus')->truncate();

        $tugasMengajarInserts = [];
        $jabatanKhususInserts = [];

        foreach ($data as $kodeGuru => $mapelData) {
            if (!isset($gurus[$kodeGuru])) continue;
            $guruId = $gurus[$kodeGuru];

            foreach ($mapelData as $kodeMapel => $kelasJam) {
                if ($kodeMapel === '_jabatan') {
                    foreach ($kelasJam as $jabatan) {
                        $jabatanKhususInserts[] = [
                            'guru_id'         => $guruId,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'jumlah_jam'      => $jabatan['jam'],
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                    continue;
                }

                if (!isset($mapels[$kodeMapel])) continue;
                $mapelId = $mapels[$kodeMapel];

                foreach ($kelasJam as $namaKelas => $jp) {
                    if (!isset($kelasMap[$namaKelas])) continue;

                    $tugasMengajarInserts[] = [
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'guru_id'         => $guruId,
                        'mapel_id'        => $mapelId,
                        'kelas_id'        => $kelasMap[$namaKelas],
                        'jumlah_jam'      => $jp,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($tugasMengajarInserts, 500) as $chunk) {
            DB::table('tugas_mengajar')->insert($chunk);
        }

        if (count($jabatanKhususInserts) > 0) {
            DB::table('guru_jabatan_khusus')->insert($jabatanKhususInserts);
        }
    }
}
