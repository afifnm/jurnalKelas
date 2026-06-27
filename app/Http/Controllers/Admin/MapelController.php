<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\MapelImport;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
{
    public function index(): View
    {
        $mapel = Mapel::withCount('jadwal')->latest()->paginate(20);
        return view('admin.mapel.index', compact('mapel'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode'],
        ]);
        $mapel = Mapel::create($request->only(['nama', 'kode']));
        return response()->json(['message' => 'Mapel berhasil ditambahkan.', 'mapel' => $mapel]);
    }

    public function update(Request $request, Mapel $mapel): JsonResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['nullable', 'string', 'max:20', "unique:mapel,kode,{$mapel->id}"],
        ]);
        $mapel->update($request->only(['nama', 'kode']));
        return response()->json(['message' => 'Mapel berhasil diperbarui.', 'mapel' => $mapel]);
    }

    public function destroy(Mapel $mapel): JsonResponse
    {
        $mapel->delete();
        return response()->json(['message' => 'Mapel berhasil dihapus.']);
    }

    public function pengajar(Mapel $mapel): JsonResponse
    {
        $jadwal = $mapel->jadwal()
            ->with(['guru:id,nama', 'kelas:id,nama', 'tahunAjaran:id,nama,semester,is_aktif'])
            ->get();

        $grouped = $jadwal
            ->groupBy('guru_id')
            ->map(function ($items) {
                $guru = $items->first()->guru;
                $kelas = $items->map(fn($j) => [
                    'nama'         => $j->kelas->nama,
                    'tahun_ajaran' => $j->tahunAjaran->nama . ' — ' . $j->tahunAjaran->semester,
                    'is_aktif'     => $j->tahunAjaran->is_aktif,
                ])->sortBy('nama')->values();

                return ['guru' => $guru->nama, 'kelas' => $kelas];
            })
            ->sortBy('guru')
            ->values();

        return response()->json(['mapel' => $mapel->nama, 'pengajar' => $grouped]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $import = new MapelImport();
        Excel::import($import, $request->file('file'));

        return response()->json([
            'message'       => "{$import->successCount} mata pelajaran berhasil diimpor.",
            'success_count' => $import->successCount,
            'errors'        => $import->errors,
        ]);
    }
}
