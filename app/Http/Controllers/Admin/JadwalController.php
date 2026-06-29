<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJadwalRequest;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'kelas', 'mapel', 'tahunAjaran', 'jamPelajaran']);

        if ($guru = $request->guru_id) {
            $query->where('guru_id', $guru);
        }
        if ($ta = $request->tahun_ajaran_id) {
            $query->where('tahun_ajaran_id', $ta);
        }

        $jadwal       = $query->orderBy('jam_pelajaran.hari')->orderBy('jam_pelajaran.jam_mulai')->paginate(20)->withQueryString();
        $guru         = User::role('guru')->orderBy('nama')->get();
        $kelas        = Kelas::orderBy('nama')->get();
        $mapel        = Mapel::orderBy('nama')->get();
        $tahunAjaran  = TahunAjaran::orderByDesc('is_aktif')->get();
        $namaHari     = Jadwal::getNamaHariList();

        return view('admin.jadwal.index', compact('jadwal', 'guru', 'kelas', 'mapel', 'tahunAjaran', 'namaHari'));
    }

    public function store(StoreJadwalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $overwriteId = $request->input('overwrite_id', 0);
        
        $excludeIds = array_filter([$overwriteId]);
        $warnings = $this->findConflicts($data, $excludeIds);

        if (count($warnings) > 0) {
            return response()->json([
                'message' => 'Jadwal bentrok',
                'errors'  => ['conflict' => $warnings],
            ], 422);
        }

        if ($overwriteId) {
            Jadwal::where('id', $overwriteId)->delete();
        }

        $jadwal  = Jadwal::create($data);
        $jadwal->load(['guru', 'kelas', 'mapel', 'jamPelajaran']);

        return response()->json([
            'message'  => 'Jadwal berhasil ditambahkan.',
            'jadwal'   => $jadwal,
        ]);
    }

    public function update(StoreJadwalRequest $request, Jadwal $jadwal): JsonResponse
    {
        $data = $request->validated();
        $overwriteId = $request->input('overwrite_id', 0);

        $excludeIds = array_filter([$jadwal->id, $overwriteId]);
        $warnings = $this->findConflicts($data, $excludeIds);

        if (count($warnings) > 0) {
            return response()->json([
                'message' => 'Jadwal bentrok',
                'errors'  => ['conflict' => $warnings],
            ], 422);
        }

        if ($overwriteId && $overwriteId != $jadwal->id) {
            Jadwal::where('id', $overwriteId)->delete();
        }

        $jadwal->update($data);

        return response()->json([
            'message'  => 'Jadwal berhasil diperbarui.',
            'jadwal'   => $jadwal->fresh()->load(['guru', 'kelas', 'mapel', 'jamPelajaran']),
        ]);
    }

    public function destroy(Jadwal $jadwal): JsonResponse
    {
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }

    private function findConflicts(array $data, array $excludeIds): array
    {
        $warnings = [];

        $baseQuery = fn(string $col, int $val) => Jadwal::with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->where($col, $val)
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('jam_pelajaran_id', $data['jam_pelajaran_id'])
            ->when(!empty($excludeIds), function($q) use ($excludeIds) {
                $q->whereNotIn('id', $excludeIds);
            })
            ->get();

        foreach ($baseQuery('guru_id', $data['guru_id']) as $konflik) {
            $jam = "Jam ke-{$konflik->jamPelajaran->jam_ke} ({$konflik->nama_hari})";
            $warnings[] = "Konflik guru: {$konflik->guru->nama} sudah mengajar {$konflik->mapel->nama} di Kelas {$konflik->kelas->nama} pada {$jam}.";
        }

        foreach ($baseQuery('kelas_id', $data['kelas_id']) as $konflik) {
            $jam = "Jam ke-{$konflik->jamPelajaran->jam_ke} ({$konflik->nama_hari})";
            $warnings[] = "Konflik kelas: Kelas ini sudah ada jadwal {$konflik->mapel->nama} ({$konflik->guru->nama}) pada {$jam}.";
        }

        return $warnings;
    }
}
