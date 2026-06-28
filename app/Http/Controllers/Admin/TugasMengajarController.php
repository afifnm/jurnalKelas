<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruJabatanKhusus;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\TugasMengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TugasMengajarController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return redirect()->route('admin.dashboard')->with('error', 'Tidak ada Tahun Ajaran aktif.');
        }

        $tahunId = $tahunAjaranAktif->id;

        // Get classes grouped by tingkat
        $kelasList = Kelas::orderBy('nama')->get();
        $kelasGrouped = [];
        foreach ($kelasList as $kelas) {
            $parts = explode(' ', $kelas->nama);
            $tingkat = $parts[0] ?? 'Lainnya'; // e.g. "X", "XI", "XII"
            $kelasGrouped[$tingkat][] = $kelas;
        }

        // Sort groups (X, XI, XII)
        $order = ['X' => 1, 'XI' => 2, 'XII' => 3];
        uksort($kelasGrouped, function ($a, $b) use ($order) {
            $orderA = $order[$a] ?? 99;
            $orderB = $order[$b] ?? 99;
            return $orderA <=> $orderB;
        });

        // Get Mapels
        $mapelList = Mapel::orderBy('nama')->get();

        // Get Gurus
        $guruList = User::whereHas('roles', function ($q) {
            $q->where('name', 'guru');
        })->orderBy('nama')->get();

        // Get existing Tugas Mengajar
        $tugasMengajar = TugasMengajar::with(['guru', 'mapel'])
            ->where('tahun_ajaran_id', $tahunId)
            ->get();

        // Get Jabatan Khusus
        $jabatanKhusus = GuruJabatanKhusus::where('tahun_ajaran_id', $tahunId)->get();

        $rowsData = [];
        $tugasByGuru = $tugasMengajar->groupBy('guru_id');

        foreach ($guruList as $guru) {
            if ($tugasByGuru->has($guru->id)) {
                $guruTugas = $tugasByGuru[$guru->id];
                $groupedMapel = $guruTugas->groupBy('mapel_id');
                
                foreach ($groupedMapel as $mapelId => $items) {
                    $first = $items->first();
                    $kelasHours = [];
                    foreach ($items as $item) {
                        if ($item->jumlah_jam > 0) {
                            $kelasHours[$item->kelas_id] = $item->jumlah_jam;
                        }
                    }
                    
                    $rowsData[] = [
                        'id_key' => $guru->id . '-' . $mapelId,
                        'guru_id' => $guru->id,
                        'guru_nama' => $guru->nama,
                        'guru_kode' => $guru->username,
                        'mapel_id' => $mapelId,
                        'mapel_nama' => $first->mapel->nama ?? '-',
                        'mapel_kode' => $first->mapel->kode ?? '-',
                        'kelas_hours' => (object) $kelasHours,
                    ];
                }
            } else {
                // Guru has no mapel assigned yet, create a dummy row
                $rowsData[] = [
                    'id_key' => $guru->id . '-empty',
                    'guru_id' => $guru->id,
                    'guru_nama' => $guru->nama,
                    'guru_kode' => $guru->username,
                    'mapel_id' => null,
                    'mapel_nama' => '-',
                    'mapel_kode' => '-',
                    'kelas_hours' => (object) [],
                ];
            }
        }

        $jabatanData = [];
        foreach ($jabatanKhusus as $jk) {
            $jabatanData[$jk->guru_id] = [
                'jumlah_jam' => $jk->jumlah_jam,
            ];
        }

        return view('admin.tugas_mengajar.index', compact(
            'tahunAjaranAktif',
            'kelasGrouped',
            'kelasList',
            'mapelList',
            'guruList',
            'rowsData',
            'jabatanData'
        ));
    }

    public function updateCell(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'jumlah_jam' => 'nullable|integer|min:0',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif.'], 400);
        }

        try {
            DB::beginTransaction();

            $jam = $request->jumlah_jam ?? 0;

            if ($jam == 0) {
                TugasMengajar::where([
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'guru_id' => $request->guru_id,
                    'mapel_id' => $request->mapel_id,
                    'kelas_id' => $request->kelas_id,
                ])->delete();
                $tugas = null;
            } else {
                $tugas = TugasMengajar::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                        'guru_id' => $request->guru_id,
                        'mapel_id' => $request->mapel_id,
                        'kelas_id' => $request->kelas_id,
                    ],
                    [
                        'jumlah_jam' => $jam,
                    ]
                );
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $tugas]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJabatan(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'jumlah_jam' => 'nullable|integer|min:0',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif.'], 400);
        }

        try {
            DB::beginTransaction();

            $jam = $request->jumlah_jam ?? 0;

            if ($jam == 0) {
                // Delete if empty
                GuruJabatanKhusus::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                    ->where('guru_id', $request->guru_id)
                    ->delete();
                $data = null;
            } else {
                $data = GuruJabatanKhusus::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                        'guru_id' => $request->guru_id,
                    ],
                    [
                        'jumlah_jam' => $jam,
                    ]
                );
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyRow(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif.'], 400);
        }

        try {
            TugasMengajar::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->where('guru_id', $request->guru_id)
                ->where('mapel_id', $request->mapel_id)
                ->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'changes' => 'required|array',
            'changes.*.guru_id' => 'required|exists:users,id',
            'changes.*.mapel_id' => 'required|exists:mapel,id',
            'changes.*.kelas_id' => 'required|exists:kelas,id',
            'changes.*.jumlah_jam' => 'nullable|integer|min:0',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif.'], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($request->changes as $change) {
                $jam = $change['jumlah_jam'] ?? 0;

                if ($jam == 0) {
                    TugasMengajar::where([
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                        'guru_id' => $change['guru_id'],
                        'mapel_id' => $change['mapel_id'],
                        'kelas_id' => $change['kelas_id'],
                    ])->delete();
                } else {
                    TugasMengajar::updateOrCreate(
                        [
                            'tahun_ajaran_id' => $tahunAjaranAktif->id,
                            'guru_id' => $change['guru_id'],
                            'mapel_id' => $change['mapel_id'],
                            'kelas_id' => $change['kelas_id'],
                        ],
                        [
                            'jumlah_jam' => $jam,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Berhasil menyimpan seluruh data tugas mengajar.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function print(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            abort(404, 'Tidak ada Tahun Ajaran aktif.');
        }

        $tahunId = $tahunAjaranAktif->id;

        // Get classes grouped by tingkat
        $kelasList = Kelas::orderBy('nama')->get();
        $kelasGrouped = [];
        foreach ($kelasList as $kelas) {
            $parts = explode(' ', $kelas->nama);
            $tingkat = $parts[0] ?? 'Lainnya';
            $kelasGrouped[$tingkat][] = $kelas;
        }

        $order = ['X' => 1, 'XI' => 2, 'XII' => 3];
        uksort($kelasGrouped, function ($a, $b) use ($order) {
            $orderA = $order[$a] ?? 99;
            $orderB = $order[$b] ?? 99;
            return $orderA <=> $orderB;
        });

        // Get Gurus sorted by Kode Guru ASC (username)
        $guruList = User::whereHas('roles', function ($q) {
            $q->where('name', 'guru');
        })->orderBy('username')->get();

        $tugasMengajar = TugasMengajar::with(['guru', 'mapel'])
            ->where('tahun_ajaran_id', $tahunId)
            ->get();

        $jabatanKhusus = GuruJabatanKhusus::where('tahun_ajaran_id', $tahunId)->get();

        $rowsData = [];
        $tugasByGuru = $tugasMengajar->groupBy('guru_id');

        foreach ($guruList as $guru) {
            if ($tugasByGuru->has($guru->id)) {
                $guruTugas = $tugasByGuru[$guru->id];
                $groupedMapel = $guruTugas->groupBy('mapel_id');
                
                foreach ($groupedMapel as $mapelId => $items) {
                    $first = $items->first();
                    $kelasHours = [];
                    foreach ($items as $item) {
                        if ($item->jumlah_jam > 0) {
                            $kelasHours[$item->kelas_id] = $item->jumlah_jam;
                        }
                    }
                    
                    $rowsData[] = [
                        'guru_id' => $guru->id,
                        'guru_nama' => $guru->nama,
                        'guru_kode' => $guru->username,
                        'mapel_id' => $mapelId,
                        'mapel_nama' => $first->mapel->nama ?? '-',
                        'mapel_kode' => $first->mapel->kode ?? '-',
                        'kelas_hours' => (object) $kelasHours,
                    ];
                }
            } else {
                $rowsData[] = [
                    'guru_id' => $guru->id,
                    'guru_nama' => $guru->nama,
                    'guru_kode' => $guru->username,
                    'mapel_id' => null,
                    'mapel_nama' => '-',
                    'mapel_kode' => '-',
                    'kelas_hours' => (object) [],
                ];
            }
        }

        $jabatanData = [];
        foreach ($jabatanKhusus as $jk) {
            $jabatanData[$jk->guru_id] = [
                'jumlah_jam' => $jk->jumlah_jam,
                'nama_jabatan' => $jk->nama_jabatan,
            ];
        }

        $sekolah = \App\Models\Sekolah::first();

        return view('admin.tugas_mengajar.print', compact(
            'tahunAjaranAktif',
            'kelasGrouped',
            'rowsData',
            'jabatanData',
            'kelasList',
            'sekolah'
        ));
    }
}
