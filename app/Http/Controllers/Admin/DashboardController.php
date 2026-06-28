<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
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

        $hariIni = now()->dayOfWeekIso;
        $jadwalHariIni = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->where('jam_pelajaran.hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $guruBelumIsi = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->where('jam_pelajaran.hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereDoesntHave('jurnal', fn($q) => $q->whereDate('tanggal', today()))
            ->get()
            ->groupBy('guru_id');

        return view('admin.dashboard', compact(
            'totalGuru', 'totalKelas', 'totalMapel', 'totalJadwal',
            'sekolah', 'tahunAktif', 'jadwalHariIni',
            'guruBelumIsi'
        ));
    }
}
