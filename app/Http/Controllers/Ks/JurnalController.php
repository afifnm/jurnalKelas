<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jurnal::with(['guru', 'kelas', 'mapel', 'lampiran', 'jadwal.jamPelajaran']);

        $periode = $request->input('periode');
        if ($periode === 'hari_ini') {
            $query->whereDate('tanggal', today());
        } elseif ($periode === 'minggu_ini') {
            $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        } elseif ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
        }

        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jurnal = $query->latest('tanggal')->paginate(20)->withQueryString();
        $guru = User::role('guru')->orderBy('nama')->get();
        $kelas = Kelas::orderBy('nama')->get();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);

        return view('ks.jurnal.index', [
            'jurnal' => $jurnal,
            'jamSesiMap' => $jamSesiMap,
            'guru' => $guru,
            'kelas' => $kelas,
            'canCreate' => false,
            'canEdit' => false,
            'breadcrumbRole' => 'Kepala Sekolah',
            'headerDesc' => 'Lihat semua jurnal mengajar yang telah diisi oleh guru',
            'indexRoute' => route('ks.jurnal.index'),
            'showRouteBase' => '/ks/jurnal',
        ]);
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        $jurnal->load(['guru', 'kelas', 'mapel', 'jadwal.jamPelajaran', 'lampiran']);

        return response()->json($jurnal->toDetailArray())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
