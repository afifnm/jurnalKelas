<?php

namespace App\Http\Controllers\Ks;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\TahunAjaran;
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

        $tahunId = $request->tahun_ajaran_id ?? $tahunAktif?->id;

        $jadwalPerKelas = collect();
        foreach ($kelasList as $kelas) {
            $jadwal = Jadwal::with(['guru', 'mapel'])
                ->where('kelas_id', $kelas->id)
                ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');

            $jadwalPerKelas[$kelas->id] = [
                'kelas'  => $kelas,
                'jadwal' => $jadwal,
            ];
        }

        return view('ks.jadwal.by-kelas', compact(
            'tahunAjaran', 'tahunAktif', 'kelasList', 'namaHari',
            'jadwalPerKelas', 'tahunId'
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

        $jadwalPerGuru = collect();
        foreach ($guruList as $guru) {
            $jadwal = Jadwal::with(['kelas', 'mapel'])
                ->where('guru_id', $guru->id)
                ->when($tahunId, fn($q) => $q->where('tahun_ajaran_id', $tahunId))
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');

            $jadwalPerGuru[$guru->id] = [
                'guru'   => $guru,
                'jadwal' => $jadwal,
            ];
        }

        return view('ks.jadwal.by-guru', compact(
            'tahunAjaran', 'tahunAktif', 'guruList', 'namaHari',
            'jadwalPerGuru', 'tahunId', 'hariIni'
        ));
    }
}
