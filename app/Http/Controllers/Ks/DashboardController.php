<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $totalGuru = User::role('guru')->where('is_active', true)->count();

        $jadwalHariIni = Jadwal::whereHas('jamPelajaran', fn($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->pluck('guru_id')
            ->unique();

        $sudahIsiHariIni = Jurnal::whereDate('tanggal', today())
            ->pluck('guru_id')
            ->unique();

        $belumIsiHariIni = User::role('guru')
            ->where('is_active', true)
            ->whereIn('id', $jadwalHariIni)
            ->whereNotIn('id', $sudahIsiHariIni)
            ->get();

        $jurnalHariIni  = Jurnal::whereDate('tanggal', today())->count();
        $jurnalBulanIni = Jurnal::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])->count();

        $jurnalTerbaru = Jurnal::with(['guru', 'kelas', 'mapel'])
            ->latest('tanggal')
            ->take(8)
            ->get();

        return view('ks.dashboard', compact(
            'totalGuru', 'belumIsiHariIni',
            'jurnalHariIni', 'jurnalBulanIni', 'jurnalTerbaru', 'tahunAktif'
        ));
    }
}
