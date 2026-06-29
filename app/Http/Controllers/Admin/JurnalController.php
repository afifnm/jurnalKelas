<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $query = Jurnal::with(['guru', 'kelas', 'mapel', 'lampiran', 'jadwal.jamPelajaran'])
            ->join('users as guru_user', 'jurnal.guru_id', '=', 'guru_user.id')
            ->select('jurnal.*')
            ->when($tahunAktif, fn($q) => $q->where('jurnal.tahun_ajaran_id', $tahunAktif->id));

        if ($request->filled('guru_id')) {
            $query->where('jurnal.guru_id', $request->guru_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('jurnal.kelas_id', $request->kelas_id);
        }

        // Shortcut periode
        $periode = $request->input('periode');
        if ($periode === 'hari_ini') {
            $query->whereDate('jurnal.tanggal', today());
        } elseif ($periode === 'minggu_ini') {
            $query->whereBetween('jurnal.tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $query->whereMonth('jurnal.tanggal', now()->month)
                  ->whereYear('jurnal.tanggal', now()->year);
        } elseif ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('jurnal.tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('jurnal.tanggal', '<=', $request->tanggal_sampai);
            }
        }

        $jurnal     = $query->orderByDesc('jurnal.tanggal')->orderBy('guru_user.nama')->paginate(25)->withQueryString();
        $guru       = User::role('guru')->orderBy('nama')->get();
        $kelas      = Kelas::orderBy('nama')->get();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);

        return view('admin.jurnal.index', [
            'jurnal'         => $jurnal,
            'jamSesiMap'     => $jamSesiMap,
            'guru'           => $guru,
            'kelas'          => $kelas,
            'tahunAktif'     => $tahunAktif,
            'canCreate'      => false,
            'canEdit'        => false,
            'breadcrumbRole' => 'Admin',
            'headerDesc'     => 'Semua jurnal mengajar',
            'indexRoute'     => route('admin.jurnal.index'),
            'showRouteBase'  => '/admin/jurnal',
        ]);
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        $jurnal->load(['guru', 'kelas', 'mapel', 'jadwal.jamPelajaran', 'lampiran']);
        $sesi = Jurnal::buildJamSesiMap(collect([$jurnal]));
        $data = $jurnal->toArray();
        $data['jam_sesi'] = $sesi[$jurnal->id] ?? null;
        $data['dalam_jam'] = $jurnal->isInputDalamJamMengajar();
        return response()->json($data);
    }
}
