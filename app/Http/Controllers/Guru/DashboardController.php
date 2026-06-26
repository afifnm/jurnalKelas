<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\KinerjaGuru;
use App\Models\TahunAjaran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $guru       = auth()->user();
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $jadwalHariIni = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_mulai')
            ->get();

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $belumDiisi = $jadwalHariIni->filter(fn($j) => ! in_array($j->id, $sudahDiisiHariIni));

        $bulanIni   = now()->format('Y-m');
        $kinerja    = KinerjaGuru::where('guru_id', $guru->id)->where('periode', $bulanIni)->first();

        $jurnalRevisi  = Jurnal::where('guru_id', $guru->id)->where('status', 'revisi')->count();
        $jurnalDraft   = Jurnal::where('guru_id', $guru->id)->where('status', 'draft')->count();

        $riwayatJurnal = Jurnal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        return view('guru.dashboard', compact(
            'jadwalHariIni', 'sudahDiisiHariIni', 'belumDiisi',
            'kinerja', 'jurnalRevisi', 'jurnalDraft', 'riwayatJurnal', 'tahunAktif'
        ));
    }
}
