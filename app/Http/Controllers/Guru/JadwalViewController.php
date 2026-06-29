<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalViewController extends Controller
{
    public function byKelas(Request $request): View
    {
        $guru        = auth()->user();
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $namaHari    = Jadwal::getNamaHariList();
        $hariIni     = now()->dayOfWeekIso;

        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $kelasId = $request->kelas_id;

        $kelasList = Kelas::orderBy('nama')->get();

        if (!$kelasId && $kelasList->isNotEmpty()) {
            $kelasId = $kelasList->first()->id;
        }

        $allJadwalKelas = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'jamPelajaran'])
            ->whereIn('kelas_id', $kelasList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get()
            ->groupBy('kelas_id');

        $jadwalPerKelas = collect();
        foreach ($kelasList as $kelas) {
            $jadwal = $allJadwalKelas->get($kelas->id, collect())->filter(fn($j) => $j->jamPelajaran !== null)->groupBy(fn($j) => $j->jamPelajaran->hari);
            $jadwalPerKelas[$kelas->id] = [
                'kelas'  => $kelas,
                'jadwal' => $jadwal,
            ];
        }

        $waktuIni     = now()->format('H:i:s');
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_mulai')->get();

        return view('jadwal.by-kelas', compact(
            'tahunAjaran', 'tahunAktif', 'kelasList', 'namaHari',
            'jadwalPerKelas', 'tahunId', 'kelasId', 'hariIni', 'waktuIni', 'jamPelajaran'
        ));
    }

    public function byGuru(Request $request): View
    {
        $guru        = auth()->user();
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $guruList    = \App\Models\User::role('guru')->where('is_active', true)->orderBy('nama')->get();
        $namaHari    = Jadwal::getNamaHariList();
        $hariIni     = now()->dayOfWeekIso;

        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $guruId  = $request->guru_id ?? $guru->id;

        $allJadwalGuru = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->whereIn('guru_id', $guruList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get()
            ->groupBy('guru_id');

        $jadwalPerGuru = collect();
        foreach ($guruList as $g) {
            $jadwal    = $allJadwalGuru->get($g->id, collect())->filter(fn($j) => $j->jamPelajaran !== null)->groupBy(fn($j) => $j->jamPelajaran->hari);
            $hariIniJ  = $jadwal->get($hariIni, collect());
            $jadwalPerGuru[$g->id] = [
                'guru'   => $g,
                'jadwal' => $jadwal,
                'grupHariIni' => Jadwal::grupkanBerurutan($hariIniJ),
            ];
        }

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $konflikIds = [];

        $waktuIni     = now()->format('H:i:s');
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_mulai')->get();

        return view('jadwal.by-guru', compact(
            'guru', 'tahunAjaran', 'tahunAktif', 'guruList', 'namaHari', 'hariIni', 'waktuIni',
            'jadwalPerGuru', 'tahunId', 'guruId', 'konflikIds', 'sudahDiisiHariIni', 'jamPelajaran'
        ));
    }

    public function printGuru(Request $request, \App\Models\User $guru): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $jadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get()
            ->filter(fn($j) => $j->jamPelajaran !== null)
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_mulai')->get()->groupBy('hari');

        return view('admin.jadwal.print.guru', compact('sekolah', 'tahunAktif', 'guru', 'jadwal', 'namaHari', 'jamPelajaran'));
    }

    public function laporanJurnalGuru(Request $request, \App\Models\User $guru): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $dari   = $request->tanggal_dari   ? Carbon::parse($request->tanggal_dari)   : now()->startOfWeek();
        $sampai = $request->tanggal_sampai ? Carbon::parse($request->tanggal_sampai) : now()->endOfWeek();

        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get();

        $allJurnal = Jurnal::where('guru_id', $guru->id)
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->with(['kelas', 'mapel'])
            ->get();

        $jurnalByJadwal   = $allJurnal->groupBy(fn($j) => $j->jadwal_id . '|' . $j->tanggal->format('Y-m-d'));
        $jurnalByFallback = $allJurnal->groupBy(fn($j) => $j->kelas_id . '|' . $j->mapel_id . '|' . $j->tanggal->format('Y-m-d'));

        $grupPerHari = Jadwal::grupkanBerurutan($allJadwal)->groupBy(fn($g) => $g['jadwal']->first()->jamPelajaran->hari);

        $laporan = [];
        $current = $dari->copy()->startOfDay();
        $end     = $sampai->copy()->startOfDay();

        while ($current->lte($end)) {
            $hariNum = $current->dayOfWeekIso;
            $grups   = $grupPerHari->get($hariNum, collect());

            if ($grups->isNotEmpty()) {
                $entries = [];
                foreach ($grups as $grup) {
                    $firstJadwal = $grup['jadwal']->first();
                    $lastJadwal  = $grup['jadwal']->last();
                    $key   = $firstJadwal->id . '|' . $current->format('Y-m-d');
                    $fbKey = $firstJadwal->kelas_id . '|' . $firstJadwal->mapel_id . '|' . $current->format('Y-m-d');
                    $jurnal = $jurnalByJadwal->get($key)?->first()
                        ?? $jurnalByFallback->get($fbKey)?->first();

                    $entries[] = ['jadwal' => $firstJadwal, 'lastJadwal' => $lastJadwal, 'jumlahJam' => count($grup['ids']), 'jurnal' => $jurnal];
                }
                $laporan[] = ['tanggal' => $current->copy(), 'hari' => $hariNum, 'entries' => $entries];
            }
            $current->addDay();
        }

        $totalSesi   = collect($laporan)->sum(fn($d) => collect($d['entries'])->sum('jumlahJam'));
        $totalDiisi  = collect($laporan)->sum(fn($d) => collect($d['entries'])->filter(fn($e) => $e['jurnal'])->sum('jumlahJam'));
        $totalKosong = $totalSesi - $totalDiisi;

        return view('admin.jadwal.print.laporan-jurnal-guru', compact(
            'sekolah', 'tahunAktif', 'guru', 'laporan', 'namaHari',
            'dari', 'sampai', 'totalSesi', 'totalDiisi', 'totalKosong'
        ));
    }

    public function printKelas(Request $request, \App\Models\Kelas $kelas): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $jadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'jamPelajaran'])
            ->where('kelas_id', $kelas->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get()
            ->filter(fn($j) => $j->jamPelajaran !== null)
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_mulai')->get()->groupBy('hari');

        return view('admin.jadwal.print.kelas', compact('sekolah', 'tahunAktif', 'kelas', 'jadwal', 'namaHari', 'jamPelajaran'));
    }

    public function laporanJurnalKelas(Request $request, \App\Models\Kelas $kelas): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $dari   = $request->tanggal_dari   ? \Carbon\Carbon::parse($request->tanggal_dari)   : now()->startOfWeek();
        $sampai = $request->tanggal_sampai ? \Carbon\Carbon::parse($request->tanggal_sampai) : now()->endOfWeek();

        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'jamPelajaran'])
            ->where('kelas_id', $kelas->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get();

        $allJurnal = \App\Models\Jurnal::where('kelas_id', $kelas->id)
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->with(['guru', 'mapel'])
            ->get();

        $jurnalByJadwal   = $allJurnal->groupBy(fn($j) => $j->jadwal_id . '|' . $j->tanggal->format('Y-m-d'));
        $jurnalByFallback = $allJurnal->groupBy(fn($j) => $j->guru_id . '|' . $j->mapel_id . '|' . $j->tanggal->format('Y-m-d'));

        $grupPerHari = Jadwal::grupkanBerurutan($allJadwal)->groupBy(fn($g) => $g['jadwal']->first()->jamPelajaran->hari);

        $laporan = [];
        $current = $dari->copy()->startOfDay();
        $end     = $sampai->copy()->startOfDay();

        while ($current->lte($end)) {
            $hariNum = $current->dayOfWeekIso;
            $grups   = $grupPerHari->get($hariNum, collect());

            if ($grups->isNotEmpty()) {
                $entries = [];
                foreach ($grups as $grup) {
                    $firstJadwal = $grup['jadwal']->first();
                    $lastJadwal  = $grup['jadwal']->last();
                    $key   = $firstJadwal->id . '|' . $current->format('Y-m-d');
                    $fbKey = $firstJadwal->guru_id . '|' . $firstJadwal->mapel_id . '|' . $current->format('Y-m-d');
                    $jurnal = $jurnalByJadwal->get($key)?->first()
                        ?? $jurnalByFallback->get($fbKey)?->first();

                    $entries[] = ['jadwal' => $firstJadwal, 'lastJadwal' => $lastJadwal, 'jumlahJam' => count($grup['ids']), 'jurnal' => $jurnal];
                }
                $laporan[] = ['tanggal' => $current->copy(), 'hari' => $hariNum, 'entries' => $entries];
            }
            $current->addDay();
        }

        $totalSesi    = collect($laporan)->sum(fn($d) => collect($d['entries'])->sum('jumlahJam'));
        $totalDiisi   = collect($laporan)->sum(fn($d) => collect($d['entries'])->filter(fn($e) => $e['jurnal'])->sum('jumlahJam'));
        $totalKosong  = $totalSesi - $totalDiisi;

        return view('admin.jadwal.print.laporan-jurnal-kelas', compact(
            'sekolah', 'tahunAktif', 'kelas', 'laporan', 'namaHari',
            'dari', 'sampai', 'totalSesi', 'totalDiisi', 'totalKosong'
        ));
    }
}
