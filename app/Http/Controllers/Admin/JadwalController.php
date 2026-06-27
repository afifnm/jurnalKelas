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
        $query = Jadwal::with(['guru', 'kelas', 'mapel', 'tahunAjaran']);

        if ($guru = $request->guru_id) {
            $query->where('guru_id', $guru);
        }
        if ($ta = $request->tahun_ajaran_id) {
            $query->where('tahun_ajaran_id', $ta);
        }

        $jadwal       = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(20)->withQueryString();
        $guru         = User::role('guru')->orderBy('nama')->get();
        $kelas        = Kelas::orderBy('nama')->get();
        $mapel        = Mapel::orderBy('nama')->get();
        $tahunAjaran  = TahunAjaran::orderByDesc('is_aktif')->get();
        $namaHari     = Jadwal::getNamaHariList();

        return view('admin.jadwal.index', compact('jadwal', 'guru', 'kelas', 'mapel', 'tahunAjaran', 'namaHari'));
    }

    public function store(StoreJadwalRequest $request): JsonResponse
    {
        $data    = $request->validated();
        $jadwal  = Jadwal::create($data);
        $jadwal->load(['guru', 'kelas', 'mapel', 'tahunAjaran']);

        $warnings = $this->findConflicts($data, $jadwal->id);

        return response()->json([
            'message'  => 'Jadwal berhasil ditambahkan.',
            'jadwal'   => $jadwal,
            'warnings' => $warnings,
        ]);
    }

    public function update(StoreJadwalRequest $request, Jadwal $jadwal): JsonResponse
    {
        $data = $request->validated();
        $jadwal->update($data);

        $warnings = $this->findConflicts($data, $jadwal->id);

        return response()->json([
            'message'  => 'Jadwal berhasil diperbarui.',
            'jadwal'   => $jadwal->fresh()->load(['guru', 'kelas', 'mapel']),
            'warnings' => $warnings,
        ]);
    }

    public function destroy(Jadwal $jadwal): JsonResponse
    {
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }

    private function findConflicts(array $data, int $excludeId): array
    {
        $warnings = [];

        $baseQuery = fn(string $col, int $val) => Jadwal::with(['guru', 'kelas', 'mapel'])
            ->where($col, $val)
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('hari', $data['hari'])
            ->where('id', '!=', $excludeId)
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->get();

        foreach ($baseQuery('guru_id', $data['guru_id']) as $konflik) {
            $mulai   = substr($konflik->jam_mulai, 0, 5);
            $selesai = substr($konflik->jam_selesai, 0, 5);
            $warnings[] = "Konflik guru: {$konflik->guru->nama} sudah mengajar {$konflik->mapel->nama} di Kelas {$konflik->kelas->nama} pada {$mulai}–{$selesai}.";
        }

        foreach ($baseQuery('kelas_id', $data['kelas_id']) as $konflik) {
            $mulai   = substr($konflik->jam_mulai, 0, 5);
            $selesai = substr($konflik->jam_selesai, 0, 5);
            $warnings[] = "Konflik kelas: Kelas ini sudah ada jadwal {$konflik->mapel->nama} ({$konflik->guru->nama}) pada {$mulai}–{$selesai}.";
        }

        return $warnings;
    }
}
