<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        $tahunAjaran = TahunAjaran::latest()->paginate(20);
        return view('admin.tahun-ajaran.index', compact('tahunAjaran'));
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
        $tahunAjaran->delete();
        return response()->json(['message' => 'Tahun ajaran berhasil dihapus.']);
    }

    public function aktivasi(TahunAjaran $tahunAjaran): JsonResponse
    {
        TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        $tahunAjaran->update(['is_aktif' => true]);
        return response()->json(['message' => "Tahun ajaran {$tahunAjaran->nama} ({$tahunAjaran->semester}) diaktifkan."]);
    }
}
