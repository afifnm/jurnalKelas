<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\KelasImport;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::withCount('jadwal')->latest()->paginate(20);
        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nama' => ['required', 'string', 'max:50', 'unique:kelas,nama']]);
        $kelas = Kelas::create(['nama' => $request->nama]);
        return response()->json(['message' => 'Kelas berhasil ditambahkan.', 'kelas' => $kelas]);
    }

    public function update(Request $request, Kelas $kelas): JsonResponse
    {
        $request->validate(['nama' => ['required', 'string', 'max:50', "unique:kelas,nama,{$kelas->id}"]]);
        $kelas->update(['nama' => $request->nama]);
        return response()->json(['message' => 'Kelas berhasil diperbarui.', 'kelas' => $kelas]);
    }

    public function destroy(Kelas $kelas): JsonResponse
    {
        $kelas->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ]);

        $import = new KelasImport();
        Excel::import($import, $request->file('file'));

        return response()->json([
            'message'       => "{$import->successCount} kelas berhasil diimpor.",
            'success_count' => $import->successCount,
            'errors'        => $import->errors,
        ]);
    }
}
