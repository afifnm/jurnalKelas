<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalGuru   = User::role('guru')->count();
        $totalKelas  = Kelas::count();
        $totalMapel  = Mapel::count();
        $totalJadwal = Jadwal::count();
        $sekolah     = Sekolah::first();
        $tahunAktif  = TahunAjaran::aktif();

        $hariIni = now()->dayOfWeekIso; // 1=Senin, 7=Minggu
        $jadwalHariIni = Jadwal::with(['guru', 'kelas', 'mapel'])
            ->where('hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_mulai')
            ->get();

        $jurnalHariIni = Jurnal::whereDate('tanggal', today())->count();
        $jurnalPending = Jurnal::where('status', 'submitted')->count();

        return view('admin.dashboard', compact(
            'totalGuru', 'totalKelas', 'totalMapel', 'totalJadwal',
            'sekolah', 'tahunAktif', 'jadwalHariIni', 'jurnalHariIni', 'jurnalPending'
        ));
    }
}
