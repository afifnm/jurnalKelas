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

        $allJadwalKelas = Jadwal::with(['guru', 'mapel'])
            ->whereIn('kelas_id', $kelasList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('kelas_id');

        $jadwalPerKelas = collect();
        foreach ($kelasList as $kelas) {
            $jadwal = $allJadwalKelas->get($kelas->id, collect())->groupBy('hari');
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

        $allJadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->whereIn('guru_id', $guruList->pluck('id'))
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('guru_id');

        $jadwalPerGuru = collect();
        foreach ($guruList as $guru) {
            $jadwal = $allJadwalGuru->get($guru->id, collect())->groupBy('hari');
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
            
        // Map jadwal by [hari]-[jam_mulai]-[jam_selesai] for easy lookup
        $jadwalPerSlot = [];
        foreach ($jadwal as $j) {
            $key = $j->hari . '-' . substr($j->jam_mulai, 0, 5) . '-' . substr($j->jam_selesai, 0, 5);
            $jadwalPerSlot[$key] = $j;
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

        $allJadwal = Jadwal::with(['guru', 'mapel', 'kelas'])
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Kelas yang punya jadwal
        $kelasList = Kelas::orderBy('nama')
            ->whereHas('jadwal', fn($q) =>
                $q->when($tahunId, fn($q2) => $q2->where('tahun_ajaran_id', $tahunId))
            )->get();

        // [hariNum => [slot_key => [kelas_id => jadwal]]]
        $jadwalByHari = $allJadwal->groupBy('hari')->map(function ($byHari) {
            return $byHari
                ->groupBy(fn($j) => $j->jam_mulai . '|' . $j->jam_selesai)
                ->sortKeys()
                ->map(fn($bySlot) => $bySlot->keyBy('kelas_id'));
        });

        $mapelUsed = $allJadwal->pluck('mapel')->unique('id')->sortBy('nama')->values();
        $guruUsed  = $allJadwal->pluck('guru')->unique('id')->sortBy('nama')->values();

        return view('admin.jadwal.print.semua', compact(
            'sekolah', 'tahunAktif', 'namaHari',
            'kelasList', 'jadwalByHari', 'mapelUsed', 'guruUsed'
        ));
    }

    public function printGuru(Request $request, User $guru): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $jadwal = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('admin.jadwal.print.guru', compact('sekolah', 'tahunAktif', 'guru', 'jadwal', 'namaHari'));
    }

    public function printKelas(Request $request, Kelas $kelas): View
    {
        $tahunId    = $request->tahun_ajaran_id ?? TahunAjaran::aktif()?->id;
        $sekolah    = Sekolah::first();
        $tahunAktif = TahunAjaran::find($tahunId);
        $namaHari   = Jadwal::getNamaHariList();

        $jadwal = Jadwal::with(['guru', 'mapel'])
            ->where('kelas_id', $kelas->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('admin.jadwal.print.kelas', compact('sekolah', 'tahunAktif', 'kelas', 'jadwal', 'namaHari'));
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
        $allJadwal = Jadwal::with(['guru', 'mapel'])
            ->where('kelas_id', $kelas->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_mulai')
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
            $jadwalHari = $allJadwal->where('hari', $hariNum)->sortBy('jam_mulai');

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

        $allJadwal = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
            ->orderBy('jam_mulai')
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
            $jadwalHari = $allJadwal->where('hari', $hariNum)->sortBy('jam_mulai');

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

        $semua = Jadwal::where('tahun_ajaran_id', $tahunId)
            ->select('id', 'guru_id', 'kelas_id', 'hari', 'jam_mulai', 'jam_selesai')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $konflikIds = [];

        foreach ($semua as $a) {
            foreach ($semua as $b) {
                if ($a->id === $b->id) continue;
                if ($a->hari !== $b->hari) continue;

                $overlap = $a->jam_mulai < $b->jam_selesai && $a->jam_selesai > $b->jam_mulai;
                if (! $overlap) continue;

                if ($a->guru_id === $b->guru_id || $a->kelas_id === $b->kelas_id) {
                    $konflikIds[$a->id] = true;
                    $konflikIds[$b->id] = true;
                }
            }
        }

        return array_keys($konflikIds);
    }
}
