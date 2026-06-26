<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function index(): View
    {
        $sekolah = Sekolah::first();
        return view('admin.sekolah.index', compact('sekolah'));
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'nama'           => ['nullable', 'string', 'max:200'],
            'nama_yayasan'   => ['nullable', 'string', 'max:200'],
            'npsn'           => ['nullable', 'string', 'max:20'],
            'alamat'         => ['nullable', 'string'],
            'kepala_sekolah' => ['nullable', 'string', 'max:100'],
            'telepon'        => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email'],
            'website'        => ['nullable', 'string', 'max:200'],
        ]);

        $sekolah = Sekolah::firstOrNew([]);
        $data    = $request->except(['_token', '_method']);

        $sekolah->fill($data)->save();

        return response()->json(['message' => 'Identitas sekolah berhasil diperbarui.']);
    }
}
