<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\GuruJabatanKhusus;
use App\Models\JamPelajaran;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\TugasMengajar;
use App\Models\User;
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
        $waktuIni    = now()->format('H:i:s');

        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;
        $kelasId = $request->kelas_id ?? $kelasList->first()?->id;

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
            $jadwal = $allJadwalKelas->get($kelas->id, collect())->filter(fn($j) => $j->jamPelajaran !== null)->groupBy(fn($j) => $j->jamPelajaran->hari);
            $jadwalPerKelas[$kelas->id] = [
                'kelas'  => $kelas,
                'jadwal' => $jadwal,
            ];
        }

        return view('jadwal.by-kelas', compact(
            'tahunAjaran', 'tahunAktif', 'kelasList', 'namaHari',
            'jadwalPerKelas', 'tahunId', 'kelasId', 'hariIni', 'waktuIni'
        ));
    }

    public function byGuru(Request $request): View
    {
        $tahunAjaran = TahunAjaran::orderByDesc('is_aktif')->get();
        $tahunAktif  = TahunAjaran::aktif();
        $guruList    = User::role('guru')->where('is_active', true)->orderBy('nama')->get();
        $namaHari    = Jadwal::getNamaHariList();
        $hariIni     = now()->dayOfWeekIso;
        $waktuIni    = now()->format('H:i:s');

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
            $jadwal = $allJadwalGuru->get($guru->id, collect())->filter(fn($j) => $j->jamPelajaran !== null)->groupBy(fn($j) => $j->jamPelajaran->hari);
            $jadwalPerGuru[$guru->id] = [
                'guru'   => $guru,
                'jadwal' => $jadwal,
            ];
        }

        return view('jadwal.by-guru', compact(
            'tahunAjaran', 'tahunAktif', 'guruList', 'namaHari',
            'jadwalPerGuru', 'tahunId', 'guruId', 'hariIni', 'waktuIni'
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

        $kelasList = Kelas::orderBy('nama')
            ->whereHas('jadwal', fn($q) =>
                $q->when($tahunId, fn($q2) => $q2->where('tahun_ajaran_id', $tahunId))
            )->get();

        $jadwalByHari = $allJadwal->filter(fn($j) => $j->jamPelajaran !== null)->groupBy(fn($j) => $j->jamPelajaran->hari)->map(function ($byHari) {
            return $byHari
                ->groupBy(fn($j) => $j->jamPelajaran->jam_mulai . '|' . $j->jamPelajaran->jam_selesai)
                ->map(fn($bySlot) => $bySlot->keyBy('kelas_id'));
        });

        $mapelUsed    = $allJadwal->pluck('mapel')->unique('id')->sortBy('nama')->values();
        $guruUsed     = $allJadwal->pluck('guru')->unique('id')->sortBy('username')->values();
        $jamPelajaran = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');

        return view('admin.jadwal.print.semua', compact(
            'sekolah', 'tahunAktif', 'namaHari',
            'kelasList', 'jadwalByHari', 'mapelUsed', 'guruUsed', 'jamPelajaran'
        ));
    }

    public function printBebanMengajar(Request $request): View
    {
        $tahunAjaranAktif = TahunAjaran::aktif();
        if (! $tahunAjaranAktif) {
            abort(404, 'Tidak ada Tahun Ajaran aktif.');
        }

        $tahunId   = $tahunAjaranAktif->id;
        $kelasList = Kelas::orderBy('nama')->get();

        $kelasGrouped = [];
        foreach ($kelasList as $kelas) {
            $parts    = explode(' ', $kelas->nama);
            $tingkat  = $parts[0] ?? 'Lainnya';
            $kelasGrouped[$tingkat][] = $kelas;
        }

        $order = ['X' => 1, 'XI' => 2, 'XII' => 3];
        uksort($kelasGrouped, fn($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));

        $guruList = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))
            ->orderBy('username')->get();

        $tugasMengajar = TugasMengajar::with(['guru', 'mapel'])
            ->where('tahun_ajaran_id', $tahunId)
            ->get();

        $jabatanKhusus = GuruJabatanKhusus::where('tahun_ajaran_id', $tahunId)->get();

        $rowsData    = [];
        $tugasByGuru = $tugasMengajar->groupBy('guru_id');

        foreach ($guruList as $guru) {
            if ($tugasByGuru->has($guru->id)) {
                foreach ($tugasByGuru[$guru->id]->groupBy('mapel_id') as $mapelId => $items) {
                    $first      = $items->first();
                    $kelasHours = [];
                    foreach ($items as $item) {
                        if ($item->jumlah_jam > 0) {
                            $kelasHours[$item->kelas_id] = $item->jumlah_jam;
                        }
                    }
                    $rowsData[] = [
                        'guru_id'    => $guru->id,
                        'guru_nama'  => $guru->nama,
                        'guru_kode'  => $guru->username,
                        'mapel_id'   => $mapelId,
                        'mapel_nama' => $first->mapel->nama ?? '-',
                        'mapel_kode' => $first->mapel->kode ?? '-',
                        'kelas_hours' => (object) $kelasHours,
                    ];
                }
            } else {
                $rowsData[] = [
                    'guru_id'    => $guru->id,
                    'guru_nama'  => $guru->nama,
                    'guru_kode'  => $guru->username,
                    'mapel_id'   => null,
                    'mapel_nama' => '-',
                    'mapel_kode' => '-',
                    'kelas_hours' => (object) [],
                ];
            }
        }

        $jabatanData = [];
        foreach ($jabatanKhusus as $jk) {
            $jabatanData[$jk->guru_id] = [
                'jumlah_jam'   => $jk->jumlah_jam,
                'nama_jabatan' => $jk->nama_jabatan,
            ];
        }

        $sekolah = Sekolah::first();

        return view('admin.tugas_mengajar.print', compact(
            'tahunAjaranAktif',
            'kelasGrouped',
            'rowsData',
            'jabatanData',
            'kelasList',
            'sekolah'
        ));
    }
}
