<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalViewController extends Controller
{
    public function byKelas(Request $request): View
    {
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $kelasList   = Kelas::orderBy('nama')->get();
        $namaHari    = Jadwal::getNamaHariList();
        $hariIni     = now()->dayOfWeekIso;

        $tahunId  = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $kelasId  = $request->kelas_id ?? $kelasList->first()?->id;

        $allJadwalKelas = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'jamPelajaran'])
            ->whereIn('kelas_id', $kelasList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy('kelas_id');

        $jadwalPerKelas = collect();
        foreach ($kelasList as $kelas) {
            $jadwal = $allJadwalKelas->get($kelas->id, collect())->groupBy(fn($j) => $j->jamPelajaran->hari);
            $jadwalPerKelas[$kelas->id] = [
                'kelas'  => $kelas,
                'jadwal' => $jadwal,
            ];
        }

        $guru         = User::role('guru')->where('is_active', true)->orderBy('nama')->get();
        $mapel        = Mapel::orderBy('nama')->get();
        $konflikIds   = $this->getKonflikIds($tahunId);
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get();

        return view('admin.jadwal.by-kelas', compact(
            'tahunAjaran', 'tahunAktif', 'kelasList', 'namaHari',
            'jadwalPerKelas', 'tahunId', 'kelasId', 'hariIni',
            'guru', 'mapel', 'konflikIds', 'jamPelajaran'
        ));
    }

    public function byGuru(Request $request): View
    {
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $guruList    = User::role('guru')->where('is_active', true)->orderBy('nama')->get();
        $namaHari    = Jadwal::getNamaHariList();
        $hariIni     = now()->dayOfWeekIso;

        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $guruId  = $request->guru_id ?? $guruList->first()?->id;

        $allJadwalGuru = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->whereIn('guru_id', $guruList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy('guru_id');

        $jadwalPerGuru = collect();
        foreach ($guruList as $guru) {
            $jadwal = $allJadwalGuru->get($guru->id, collect())->groupBy(fn($j) => $j->jamPelajaran->hari);
            $jadwalPerGuru[$guru->id] = [
                'guru'   => $guru,
                'jadwal' => $jadwal,
            ];
        }

        $kelas        = Kelas::orderBy('nama')->get();
        $mapel        = Mapel::orderBy('nama')->get();
        $konflikIds   = $this->getKonflikIds($tahunId);
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get();

        return view('admin.jadwal.by-guru', compact(
            'tahunAjaran', 'tahunAktif', 'guruList', 'namaHari',
            'jadwalPerGuru', 'tahunId', 'hariIni', 'guruId',
            'kelas', 'mapel', 'konflikIds', 'jamPelajaran'
        ));
    }

    public function mapping(Request $request): View
    {
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $kelasList   = Kelas::orderBy('nama')->get();
        $namaHari    = Jadwal::getNamaHariList();
        
        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $kelasId = $request->kelas_id ?? $kelasList->first()?->id;

        $jadwal = Jadwal::with(['guru', 'mapel'])
            ->where('kelas_id', $kelasId)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->get();
            
        // Map jadwal by jam_pelajaran_id for easy lookup
        $jadwalPerSlot = [];
        foreach ($jadwal as $j) {
            $jadwalPerSlot[$j->jam_pelajaran_id] = $j;
        }

        $guru         = User::role('guru')->where('is_active', true)->orderBy('nama')->get();
        $mapel        = Mapel::orderBy('nama')->get();
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');
        $kelasAktif   = $kelasList->firstWhere('id', $kelasId);

        return view('admin.jadwal.mapping', compact(
            'tahunAjaran', 'tahunAktif', 'kelasList', 'namaHari',
            'tahunId', 'kelasId', 'kelasAktif', 'jadwalPerSlot',
            'guru', 'mapel', 'jamPelajaran'
        ));
    }

    public function printSemua(Request $request): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'kelas', 'jamPelajaran'])
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.hari')
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        // Kelas yang punya jadwal
        $kelasList = Kelas::orderBy('nama')
            ->whereHas('jadwal', fn($q) =>
                $q->when($tahunId, fn($q2) => $q2->where('tahun_ajaran_id', $tahunId))
            )->get();

        // [hariNum => [jam_mulai|jam_selesai => [kelas_id => jadwal]]]
        $jadwalByHari = $allJadwal->groupBy(fn($j) => $j->jamPelajaran->hari)->map(function ($byHari) {
            return $byHari
                ->groupBy(fn($j) => $j->jamPelajaran->jam_mulai . '|' . $j->jamPelajaran->jam_selesai)
                ->map(fn($bySlot) => $bySlot->keyBy('kelas_id'));
        });

        $mapelUsed = $allJadwal->pluck('mapel')->unique('id')->sortBy('nama')->values();
        $guruUsed  = $allJadwal->pluck('guru')->unique('id')->sortBy('username')->values();

        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');

        return view('admin.jadwal.print.semua', compact(
            'sekolah', 'tahunAktif', 'namaHari',
            'kelasList', 'jadwalByHari', 'mapelUsed', 'guruUsed', 'jamPelajaran'
        ));
    }

    public function printGuru(Request $request, User $guru): View
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
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');

        return view('admin.jadwal.print.guru', compact('sekolah', 'tahunAktif', 'guru', 'jadwal', 'namaHari', 'jamPelajaran'));
    }

    public function printKelas(Request $request, Kelas $kelas): View
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
            ->orderBy('jam_pelajaran.jam_ke')
            ->get()
            ->groupBy(fn($j) => $j->jamPelajaran->hari);

        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');

        return view('admin.jadwal.print.kelas', compact('sekolah', 'tahunAktif', 'kelas', 'jadwal', 'namaHari', 'jamPelajaran'));
    }

    public function laporanJurnalKelas(Request $request, Kelas $kelas): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $dari   = $request->tanggal_dari   ? Carbon::parse($request->tanggal_dari)   : now()->startOfWeek();
        $sampai = $request->tanggal_sampai ? Carbon::parse($request->tanggal_sampai) : now()->endOfWeek();

        // Load all jadwal for this kelas (no date filter – jadwal is recurring)
        $allJadwal = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['guru', 'mapel', 'jamPelajaran'])
            ->where('kelas_id', $kelas->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        // Pre-load all jurnal in the period to avoid N+1
        $allJurnal = Jurnal::where('kelas_id', $kelas->id)
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->with(['guru', 'mapel'])
            ->get();

        $jurnalByJadwal   = $allJurnal->groupBy(fn($j) => $j->jadwal_id . '|' . $j->tanggal->format('Y-m-d'));
        $jurnalByFallback = $allJurnal->groupBy(fn($j) => $j->guru_id . '|' . $j->mapel_id . '|' . $j->tanggal->format('Y-m-d'));

        $laporan = [];
        $current = $dari->copy()->startOfDay();
        $end     = $sampai->copy()->startOfDay();

        while ($current->lte($end)) {
            $hariNum    = $current->dayOfWeekIso;
            $jadwalHari = $allJadwal->filter(fn($j) => $j->jamPelajaran->hari == $hariNum)->sortBy(fn($j) => $j->jamPelajaran->jam_ke);

            if ($jadwalHari->isNotEmpty()) {
                $entries = [];
                foreach ($jadwalHari as $jadwal) {
                    $key    = $jadwal->id . '|' . $current->format('Y-m-d');
                    $fbKey  = $jadwal->guru_id . '|' . $jadwal->mapel_id . '|' . $current->format('Y-m-d');
                    $jurnal = $jurnalByJadwal->get($key)?->first()
                        ?? $jurnalByFallback->get($fbKey)?->first();

                    $entries[] = ['jadwal' => $jadwal, 'jurnal' => $jurnal];
                }
                $laporan[] = ['tanggal' => $current->copy(), 'hari' => $hariNum, 'entries' => $entries];
            }
            $current->addDay();
        }

        $totalSesi    = collect($laporan)->sum(fn($d) => count($d['entries']));
        $totalDiisi   = collect($laporan)->sum(fn($d) => collect($d['entries'])->filter(fn($e) => $e['jurnal'])->count());
        $totalKosong  = $totalSesi - $totalDiisi;

        return view('admin.jadwal.print.laporan-jurnal-kelas', compact(
            'sekolah', 'tahunAktif', 'kelas', 'laporan', 'namaHari',
            'dari', 'sampai', 'totalSesi', 'totalDiisi', 'totalKosong'
        ));
    }

    public function laporanJurnalGuru(Request $request, User $guru): View
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
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $allJurnal = Jurnal::where('guru_id', $guru->id)
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->with(['kelas', 'mapel'])
            ->get();

        $jurnalByJadwal   = $allJurnal->groupBy(fn($j) => $j->jadwal_id . '|' . $j->tanggal->format('Y-m-d'));
        $jurnalByFallback = $allJurnal->groupBy(fn($j) => $j->kelas_id . '|' . $j->mapel_id . '|' . $j->tanggal->format('Y-m-d'));

        $laporan = [];
        $current = $dari->copy()->startOfDay();
        $end     = $sampai->copy()->startOfDay();

        while ($current->lte($end)) {
            $hariNum    = $current->dayOfWeekIso;
            $jadwalHari = $allJadwal->filter(fn($j) => $j->jamPelajaran->hari == $hariNum)->sortBy(fn($j) => $j->jamPelajaran->jam_ke);

            if ($jadwalHari->isNotEmpty()) {
                $entries = [];
                foreach ($jadwalHari as $jadwal) {
                    $key    = $jadwal->id . '|' . $current->format('Y-m-d');
                    $fbKey  = $jadwal->kelas_id . '|' . $jadwal->mapel_id . '|' . $current->format('Y-m-d');
                    $jurnal = $jurnalByJadwal->get($key)?->first()
                        ?? $jurnalByFallback->get($fbKey)?->first();

                    $entries[] = ['jadwal' => $jadwal, 'jurnal' => $jurnal];
                }
                $laporan[] = ['tanggal' => $current->copy(), 'hari' => $hariNum, 'entries' => $entries];
            }
            $current->addDay();
        }

        $totalSesi   = collect($laporan)->sum(fn($d) => count($d['entries']));
        $totalDiisi  = collect($laporan)->sum(fn($d) => collect($d['entries'])->filter(fn($e) => $e['jurnal'])->count());
        $totalKosong = $totalSesi - $totalDiisi;

        return view('admin.jadwal.print.laporan-jurnal-guru', compact(
            'sekolah', 'tahunAktif', 'guru', 'laporan', 'namaHari',
            'dari', 'sampai', 'totalSesi', 'totalDiisi', 'totalKosong'
        ));
    }

    private function getKonflikIds(?int $tahunId): array
    {
        if (! $tahunId) {
            return [];
        }

        $semuaGrouped = Jadwal::where('tahun_ajaran_id', $tahunId)
            ->select('id', 'guru_id', 'kelas_id', 'jam_pelajaran_id')
            ->get()
            ->groupBy('jam_pelajaran_id');

        $konflikIds = [];

        foreach ($semuaGrouped as $slotJadwal) {
            $slotJadwal = $slotJadwal->values();
            $count = $slotJadwal->count();
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $a = $slotJadwal[$i];
                    $b = $slotJadwal[$j];
                    if ($a->guru_id === $b->guru_id || $a->kelas_id === $b->kelas_id) {
                        $konflikIds[$a->id] = true;
                        $konflikIds[$b->id] = true;
                    }
                }
            }
        }

        return array_keys($konflikIds);
    }
}
