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
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get()
            ->filter(fn($j) => $j->jamPelajaran !== null)
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jadwalHariIni = $jadwalMinggu->get($hariIni, collect());

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $grupJadwalHariIni = Jadwal::grupkanBerurutan($jadwalHariIni->sortBy(fn($j) => $j->jamPelajaran?->jam_mulai));
        $grupJadwalMinggu  = $jadwalMinggu->map(fn($hariItems) => Jadwal::grupkanBerurutan($hariItems->sortBy(fn($j) => $j->jamPelajaran?->jam_mulai)));

        // Belum diisi = grup yang belum ada jurnalnya (1 grup = 1 input jurnal)
        $belumDiisi = $grupJadwalHariIni->filter(
            fn($grup) => count(array_intersect($grup['ids'], $sudahDiisiHariIni)) === 0
        );

        $jurnalBulanIni = Jurnal::where('guru_id', $guru->id)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])
            ->count();

        $jurnalTerlambatBulanIni = 0;

        $riwayatJurnal = Jurnal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $namaHari  = Jadwal::getNamaHariList();
        $totalSesi = $jadwalMinggu->flatten()->count();

        return view('guru.dashboard', compact(
            'jadwalHariIni', 'jadwalMinggu', 'sudahDiisiHariIni', 'belumDiisi',
            'grupJadwalHariIni', 'grupJadwalMinggu',
            'jurnalBulanIni', 'jurnalTerlambatBulanIni', 'riwayatJurnal',
            'tahunAktif', 'namaHari', 'hariIni', 'totalSesi'
        ));
    }
}
