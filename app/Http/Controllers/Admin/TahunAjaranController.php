<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        $tahunAjaran    = TahunAjaran::withCount('jadwal')->orderBy('nama')->paginate(20);
        $tahunAjaranAll = TahunAjaran::withCount('jadwal')->orderBy('nama')->get();
        return view('admin.tahun-ajaran.index', compact('tahunAjaran', 'tahunAjaranAll'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['boolean'],
        ]);

        if ($request->boolean('is_aktif')) {
            TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        $ta = TahunAjaran::create([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return response()->json(['message' => 'Tahun ajaran berhasil ditambahkan.', 'data' => $ta]);
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['boolean'],
        ]);

        if ($request->boolean('is_aktif')) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_aktif' => false]);
        }

        $tahunAjaran->update([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return response()->json(['message' => 'Tahun ajaran berhasil diperbarui.', 'data' => $tahunAjaran]);
    }

    public function destroy(TahunAjaran $tahunAjaran): JsonResponse
    {
        if ($tahunAjaran->is_aktif) {
            return response()->json(['message' => 'Tahun ajaran aktif tidak bisa dihapus.'], 422);
        }
        $tahunAjaran->jadwal()->delete();
        $tahunAjaran->delete();
        return response()->json(['message' => 'Tahun ajaran berhasil dihapus.']);
    }

    public function aktivasi(TahunAjaran $tahunAjaran): JsonResponse
    {
        TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        $tahunAjaran->update(['is_aktif' => true]);
        return response()->json(['message' => "Tahun ajaran {$tahunAjaran->nama} ({$tahunAjaran->semester}) diaktifkan."]);
    }

    public function cloneJadwal(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $request->validate([
            'source_id' => ['required', 'integer', 'exists:tahun_ajaran,id'],
        ]);

        $source = TahunAjaran::findOrFail($request->source_id);

        $tahunAjaran->jadwal()->forceDelete();

        $jadwalData = [];
        $now = now();
        foreach ($source->jadwal as $j) {
            $jadwalData[] = [
                'guru_id'         => $j->guru_id,
                'kelas_id'        => $j->kelas_id,
                'mapel_id'        => $j->mapel_id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'hari'            => $j->hari,
                'jam_mulai'       => $j->jam_mulai,
                'jam_selesai'     => $j->jam_selesai,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (!empty($jadwalData)) {
            Jadwal::insert($jadwalData);
        }

        $count = $source->jadwal->count();
        return response()->json([
            'message' => "Berhasil menyalin {$count} jadwal dari {$source->label} ke {$tahunAjaran->label}.",
        ]);
    }
}
