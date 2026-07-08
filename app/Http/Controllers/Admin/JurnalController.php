<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $query = $this->filteredQuery($request, $tahunAktif);

        $jurnal = $query->orderByDesc('jurnal.tanggal')->orderBy('guru_user.nama')->paginate(25)->withQueryString();
        $guru = User::role('guru')->orderBy('nama')->get();
        $kelas = Kelas::orderBy('nama')->get();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);

        return view('admin.jurnal.index', [
            'jurnal' => $jurnal,
            'jamSesiMap' => $jamSesiMap,
            'guru' => $guru,
            'kelas' => $kelas,
            'tahunAktif' => $tahunAktif,
            'canCreate' => false,
            'canEdit' => false,
            'breadcrumbRole' => 'Admin',
            'headerDesc' => 'Semua jurnal mengajar',
            'indexRoute' => route('admin.jurnal.index'),
            'showRouteBase' => '/admin/jurnal',
        ]);
    }

    public function cetak(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $jurnal = $this->filteredQuery($request, $tahunAktif)
            ->orderByDesc('jurnal.tanggal')
            ->orderBy('guru_user.nama')
            ->get();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);
        $totalDalamJam = $jurnal->filter(
            fn (Jurnal $item) => $item->isInputDalamJamMengajar($jamSesiMap[$item->id] ?? null)
        )->count();

        return view('admin.jurnal.print', [
            'jurnal' => $jurnal,
            'jamSesiMap' => $jamSesiMap,
            'totalDalamJam' => $totalDalamJam,
            'totalLuarJam' => $jurnal->count() - $totalDalamJam,
            'sekolah' => Sekolah::first(),
            'tahunAktif' => $tahunAktif,
            'guruFilter' => $request->filled('guru_id') ? User::find($request->integer('guru_id')) : null,
            'kelasFilter' => $request->filled('kelas_id') ? Kelas::find($request->integer('kelas_id')) : null,
            'periodeLabel' => $this->periodeLabel($request),
        ]);
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        $jurnal->load(['guru', 'kelas', 'mapel', 'jadwal.jamPelajaran', 'lampiran']);

        return response()->json($jurnal->toDetailArray())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    private function filteredQuery(Request $request, ?TahunAjaran $tahunAktif): Builder
    {
        $query = Jurnal::with(['guru', 'kelas', 'mapel', 'lampiran', 'jadwal.jamPelajaran'])
            ->join('users as guru_user', 'jurnal.guru_id', '=', 'guru_user.id')
            ->select('jurnal.*')
            ->when($tahunAktif, fn ($q) => $q->where('jurnal.tahun_ajaran_id', $tahunAktif->id));

        if ($request->filled('guru_id')) {
            $query->where('jurnal.guru_id', $request->integer('guru_id'));
        }
        if ($request->filled('kelas_id')) {
            $query->where('jurnal.kelas_id', $request->integer('kelas_id'));
        }

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
                $query->whereDate('jurnal.tanggal', '>=', $request->date('tanggal_dari'));
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('jurnal.tanggal', '<=', $request->date('tanggal_sampai'));
            }
        }

        return $query;
    }

    private function periodeLabel(Request $request): string
    {
        return match ($request->input('periode')) {
            'hari_ini' => 'Hari ini, '.today()->locale('id')->translatedFormat('j F Y'),
            'minggu_ini' => now()->startOfWeek()->format('d/m/Y').' – '.now()->endOfWeek()->format('d/m/Y'),
            'bulan_ini' => now()->locale('id')->translatedFormat('F Y'),
            default => match (true) {
                $request->filled('tanggal_dari') && $request->filled('tanggal_sampai') => $request->date('tanggal_dari')->format('d/m/Y').' – '.$request->date('tanggal_sampai')->format('d/m/Y'),
                $request->filled('tanggal_dari') => 'Mulai '.$request->date('tanggal_dari')->format('d/m/Y'),
                $request->filled('tanggal_sampai') => 'Sampai '.$request->date('tanggal_sampai')->format('d/m/Y'),
                default => 'Semua tanggal',
            },
        };
    }
}
