<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\KinerjaGuru;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $totalGuru  = User::role('guru')->where('is_active', true)->count();

        $jadwalHariIni = Jadwal::where('hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->pluck('guru_id')
            ->unique();

        $sudahIsHariIni = Jurnal::whereDate('tanggal', today())
            ->pluck('guru_id')
            ->unique();

        $belumIsiHariIni = User::role('guru')
            ->where('is_active', true)
            ->whereIn('id', $jadwalHariIni)
            ->whereNotIn('id', $sudahIsHariIni)
            ->get();

        $jurnalPending  = Jurnal::where('status', 'submitted')->count();

        $bulanIni = now()->format('Y-m');
        $kinerjaGuru = KinerjaGuru::with('guru')
            ->where('periode', $bulanIni)
            ->orderByDesc('skor_kinerja')
            ->get();

        $avgKepatuhan = $kinerjaGuru->avg('persen_kepatuhan') ?? 0;
        $avgTerlambat = $kinerjaGuru->avg('rata_keterlambatan_menit') ?? 0;

        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->translatedFormat('M Y');
            $avg = KinerjaGuru::where('periode', $bulan)->avg('persen_kepatuhan') ?? 0;
            $tren[] = ['bulan' => $label, 'kepatuhan' => round($avg, 1)];
        }

        return view('ks.dashboard', compact(
            'totalGuru', 'belumIsiHariIni', 'jurnalPending',
            'kinerjaGuru', 'avgKepatuhan', 'avgTerlambat', 'tren', 'tahunAktif'
        ));
    }
}
