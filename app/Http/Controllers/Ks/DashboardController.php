<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni = now()->dayOfWeekIso;
        $waktuIni = now()->format('H:i:s');

        $totalGuru = User::role('guru')->where('is_active', true)->count();

        // Jadwal hari ini (dengan detail untuk modal)
        $jadwalHariIniQuery = Jadwal::whereHas('jamPelajaran', fn ($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran']);

        $semualJadwalHariIni = $jadwalHariIniQuery->get();
        $jadwalGuruIds = $semualJadwalHariIni->pluck('guru_id')->unique();

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

        // Mengajar hari ini: dikelompokkan per guru → per grup jam berurutan
        $mengajarSekarang = $semualJadwalHariIni
            ->filter(fn ($j) => $j->jamPelajaran !== null)
            ->groupBy('guru_id')
            ->map(fn ($jadwalGuru) => Jadwal::grupkanBerurutan($jadwalGuru->sortBy(fn ($j) => $j->jamPelajaran->jam_mulai)))
            ->values()
            ->flatten(1)
            ->sortBy(fn ($grup) => $grup['jadwal']->first()->jamPelajaran->jam_mulai)
            ->values();

        $jurnalHariIni = Jurnal::whereDate('tanggal', today())->count();
        $jurnalBulanIni = Jurnal::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])->count();

        $jurnalTerbaru = Jurnal::with(['guru', 'kelas', 'mapel', 'jadwal.jamPelajaran'])
            ->latest('tanggal')
            ->take(8)
            ->get();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnalTerbaru);

        // Tren kepatuhan 30 hari terakhir
        $trenKepatuhan = $this->getTrenKepatuhan($tahunAktif);

        return view('dashboard.index', compact(
            'totalGuru', 'belumIsiHariIni', 'jadwalBelumIsi',
            'mengajarSekarang',
            'jurnalHariIni', 'jurnalBulanIni', 'jurnalTerbaru', 'jamSesiMap', 'tahunAktif',
            'trenKepatuhan'
        ));
    }

    private function getTrenKepatuhan(?TahunAjaran $tahunAktif): array
    {
        $hasil = [];

        // Guru per hari-of-week berdasarkan jadwal aktif (1=Senin ... 7=Minggu)
        $jadwalAktif = Jadwal::when($tahunAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with('jamPelajaran')
            ->get()
            ->filter(fn ($j) => $j->jamPelajaran !== null)
            ->groupBy(fn ($j) => $j->jamPelajaran->hari) // hari: 1-7
            ->map(fn ($items) => $items->pluck('guru_id')->unique()->count());

        // Jurnal 30 hari terakhir: hitung guru unik per tanggal
        $jurnalPerHari = Jurnal::select('tanggal', DB::raw('COUNT(DISTINCT guru_id) as jumlah_isi'))
            ->where('tanggal', '>=', now()->subDays(29)->toDateString())
            ->groupBy('tanggal')
            ->get()
            ->keyBy(fn ($r) => $r->tanggal->toDateString());

        for ($i = 29; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDate();
            $tglStr = now()->subDays($i)->toDateString();
            $hariOfWeek = (int) now()->subDays($i)->dayOfWeekIso; // 1=Senin
            $totalGuru = $jadwalAktif->get($hariOfWeek, 0);
            $jumlahIsi = $jurnalPerHari->has($tglStr) ? (int) $jurnalPerHari[$tglStr]->jumlah_isi : 0;

            $persen = $totalGuru > 0 ? round(($jumlahIsi / $totalGuru) * 100) : null;

            // Hari Minggu / hari tanpa jadwal = null (tidak ditampilkan sebagai 0%)
            if ($totalGuru === 0) {
                $persen = null;
            }

            $hasil[] = [
                'tanggal' => $tglStr,
                'label' => Carbon::parse($tgl)->translatedFormat('D j M'),
                'persen' => $persen,
                'isi' => $jumlahIsi,
                'total' => $totalGuru,
            ];
        }

        return $hasil;
    }

    public function mengajarSekarang(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni = now()->dayOfWeekIso;

        $semuaJadwal = Jadwal::whereHas('jamPelajaran', fn ($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->get()
            ->filter(fn ($j) => $j->jamPelajaran !== null)
            ->sortBy(fn ($j) => $j->jamPelajaran?->jam_mulai)
            ->values();

        $jurnalsToday = Jurnal::whereDate('tanggal', today())->get();

        // Kelompokkan per guru → per grup jam berurutan (mapel+kelas sama)
        $mengajarSekarang = $semuaJadwal
            ->groupBy('guru_id')
            ->map(fn ($jadwalGuru) => Jadwal::grupkanBerurutan($jadwalGuru))
            ->values()
            ->flatten(1) // jadi flat collection of grup
            ->sortBy(fn ($grup) => $grup['jadwal']->first()->jamPelajaran->jam_mulai)
            ->map(function ($grup) use ($jurnalsToday) {
                $first = $grup['jadwal']->first();
                $grup['jurnal'] = $jurnalsToday->first(function ($j) use ($grup, $first) {
                    return in_array($j->jadwal_id, $grup['ids']) ||
                           ($j->guru_id == $first->guru_id && $j->mapel_id == $first->mapel_id && $j->kelas_id == $first->kelas_id);
                });

                return $grup;
            })
            ->values();

        return view('dashboard.mengajar-sekarang', compact('mengajarSekarang', 'tahunAktif'));
    }

    public function belumIsiJurnal(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $hariIni = now()->dayOfWeekIso;

        $semualJadwalHariIni = Jadwal::whereHas('jamPelajaran', fn ($q) => $q->where('hari', $hariIni))
            ->when($tahunAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->with(['guru', 'kelas', 'mapel', 'jamPelajaran'])
            ->get();

        $jadwalGuruIds = $semualJadwalHariIni->pluck('guru_id')->unique();
        $sudahIsiIds = Jurnal::whereDate('tanggal', today())->pluck('guru_id')->unique();
        $belumIsiGuruIds = $jadwalGuruIds->diff($sudahIsiIds);

        $belumIsiHariIni = User::role('guru')
            ->where('is_active', true)
            ->whereIn('id', $belumIsiGuruIds)
            ->orderBy('nama')
            ->get();

        $jadwalBelumIsi = $semualJadwalHariIni
            ->whereIn('guru_id', $belumIsiGuruIds->values())
            ->groupBy('guru_id');

        return view('dashboard.belum-isi-jurnal', compact('belumIsiHariIni', 'jadwalBelumIsi', 'tahunAktif'));
    }
}
