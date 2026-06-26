<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
