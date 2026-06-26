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
        $jadwal = Jadwal::create($request->validated());
        $jadwal->load(['guru', 'kelas', 'mapel', 'tahunAjaran']);
        return response()->json(['message' => 'Jadwal berhasil ditambahkan.', 'jadwal' => $jadwal]);
    }

    public function update(StoreJadwalRequest $request, Jadwal $jadwal): JsonResponse
    {
        $jadwal->update($request->validated());
        return response()->json(['message' => 'Jadwal berhasil diperbarui.', 'jadwal' => $jadwal->fresh()->load(['guru', 'kelas', 'mapel'])]);
    }

    public function destroy(Jadwal $jadwal): JsonResponse
    {
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}
