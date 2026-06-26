<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidasiJurnalRequest;
use App\Models\Jurnal;
use App\Models\JurnalLog;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ValidasiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jurnal::with(['guru', 'kelas', 'mapel', 'lampiran']);

        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['submitted', 'validated', 'revisi']);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $jurnal = $query->latest('tanggal')->paginate(20)->withQueryString();
        $guru   = User::role('guru')->orderBy('nama')->get();
        $kelas  = Kelas::orderBy('nama')->get();
        $mapel  = Mapel::orderBy('nama')->get();

        return view('ks.validasi.index', compact('jurnal', 'guru', 'kelas', 'mapel'));
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        return response()->json($jurnal->load(['guru', 'kelas', 'mapel', 'jadwal', 'lampiran', 'log.user', 'validator']));
    }

    public function validasi(ValidasiJurnalRequest $request, Jurnal $jurnal): JsonResponse
    {
        $this->authorize('validate', $jurnal);

        $aksi = $request->aksi;

        $jurnal->update([
            'status'           => $aksi,
            'validated_by'     => auth()->id(),
            'validated_at'     => now(),
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        $keterangan = $aksi === 'validated'
            ? 'Jurnal divalidasi/disetujui.'
            : 'Jurnal diminta revisi: ' . $request->catatan_validasi;

        JurnalLog::create([
            'jurnal_id'  => $jurnal->id,
            'user_id'    => auth()->id(),
            'aksi'       => $aksi,
            'keterangan' => $keterangan,
        ]);

        $message = $aksi === 'validated'
            ? 'Jurnal berhasil divalidasi.'
            : 'Jurnal dikembalikan untuk revisi.';

        return response()->json(['message' => $message]);
    }
}
