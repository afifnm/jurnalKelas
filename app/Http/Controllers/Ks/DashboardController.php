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
        $waktuIni   = now()->format('H:i:s');

        $totalGuru = User::role('guru')->where('is_active', true)->count();

        // Jadwal hari ini (dengan detail untuk modal)
        $jadwalHariIniQuery = Jadwal::whereHas('jamPelajaran', fn($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran']);

        $semualJadwalHariIni = $jadwalHariIniQuery->get();
        $jadwalGuruIds       = $semualJadwalHariIni->pluck('guru_id')->unique();

        $sudahIsiHariIni = Jurnal::whereDate('tanggal', today())
            ->pluck('guru_id')
            ->unique();

        // Guru belum isi: jadwal hari ini tapi belum ada jurnal hari ini
        // Sertakan semua jadwal mereka hari ini untuk detail modal
        $belumIsiGuruIds = $jadwalGuruIds->diff($sudahIsiHariIni);

        $belumIsiHariIni = User::role('guru')
            ->where('is_active', true)
            ->whereIn('id', $belumIsiGuruIds)
            ->get();

        // Detail jadwal per guru yang belum isi (untuk modal)
        $jadwalBelumIsi = $semualJadwalHariIni
            ->whereIn('guru_id', $belumIsiGuruIds->values())
            ->groupBy('guru_id');

        // Mengajar hari ini: semua jadwal hari ini, urutkan per jam
        $mengajarSekarang = $semualJadwalHariIni
            ->filter(fn($j) => $j->jamPelajaran !== null)
            ->sortBy(fn($j) => $j->jamPelajaran->jam_ke);

        $jurnalHariIni  = Jurnal::whereDate('tanggal', today())->count();
        $jurnalBulanIni = Jurnal::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])->count();

        $jurnalTerbaru = Jurnal::with(['guru', 'kelas', 'mapel'])
            ->latest('tanggal')
            ->take(8)
            ->get();

        return view('ks.dashboard', compact(
            'totalGuru', 'belumIsiHariIni', 'jadwalBelumIsi',
            'mengajarSekarang',
            'jurnalHariIni', 'jurnalBulanIni', 'jurnalTerbaru', 'tahunAktif'
        ));
    }

    public function mengajarSekarang(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $mengajarSekarang = Jadwal::whereHas('jamPelajaran', fn($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->get()
            ->filter(fn($j) => $j->jamPelajaran !== null)
            ->sortBy(fn($j) => $j->jamPelajaran->jam_ke)
            ->values();

        return view('ks.dashboard.mengajar-sekarang', compact('mengajarSekarang', 'tahunAktif'));
    }

    public function belumIsiJurnal(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni    = now()->dayOfWeekIso;

        $semualJadwalHariIni = Jadwal::whereHas('jamPelajaran', fn($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->get();

        $jadwalGuruIds   = $semualJadwalHariIni->pluck('guru_id')->unique();
        $sudahIsiIds     = \App\Models\Jurnal::whereDate('tanggal', today())->pluck('guru_id')->unique();
        $belumIsiGuruIds = $jadwalGuruIds->diff($sudahIsiIds);

        $belumIsiHariIni = \App\Models\User::role('guru')
            ->where('is_active', true)
            ->whereIn('id', $belumIsiGuruIds)
            ->orderBy('nama')
            ->get();

        $jadwalBelumIsi = $semualJadwalHariIni
            ->whereIn('guru_id', $belumIsiGuruIds->values())
            ->groupBy('guru_id');

        return view('ks.dashboard.belum-isi-jurnal', compact('belumIsiHariIni', 'jadwalBelumIsi', 'tahunAktif'));
    }
}
