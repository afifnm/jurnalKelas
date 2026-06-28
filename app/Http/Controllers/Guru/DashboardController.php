<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $guru       = auth()->user();
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $jadwalMinggu = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jadwalHariIni = $jadwalMinggu->get($hariIni, collect());

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $belumDiisi = $jadwalHariIni->filter(fn($j) => ! in_array($j->id, $sudahDiisiHariIni));

        $jurnalBulanIni = Jurnal::where('guru_id', $guru->id)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])
            ->count();

        $jurnalTerlambatBulanIni = Jurnal::where('guru_id', $guru->id)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])
            ->where('is_terlambat', true)
            ->count();

        $riwayatJurnal = Jurnal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $namaHari  = Jadwal::getNamaHariList();
        $totalSesi = $jadwalMinggu->flatten()->count();

        return view('guru.dashboard', compact(
            'jadwalHariIni', 'jadwalMinggu', 'sudahDiisiHariIni', 'belumDiisi',
            'jurnalBulanIni', 'jurnalTerlambatBulanIni', 'riwayatJurnal',
            'tahunAktif', 'namaHari', 'hariIni', 'totalSesi'
        ));
    }
}
