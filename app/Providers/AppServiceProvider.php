<?php

namespace App\Providers;

use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                $view->with('notifData', ['count' => 0, 'items' => [], 'role' => null]);
                return;
            }

            $user       = Auth::user();
            $tahunAktif = TahunAjaran::aktif();
            $hariIni    = now()->dayOfWeekIso;
            $items      = [];
            $count      = 0;

            if ($user->hasRole('guru')) {
                $jadwalHariIni = Jadwal::select('jadwal.*')
                    ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
                    ->with(['kelas', 'mapel', 'jamPelajaran'])
                    ->where('guru_id', $user->id)
                    ->where('jam_pelajaran.hari', $hariIni)
                    ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->orderBy('jam_pelajaran.jam_ke')
                    ->get();

                $sudahDiisi = Jurnal::where('guru_id', $user->id)
                    ->whereDate('tanggal', today())
                    ->pluck('jadwal_id')
                    ->toArray();

                $belumDiisi = $jadwalHariIni->filter(fn($j) => ! in_array($j->id, $sudahDiisi));
                $count      = $belumDiisi->count();

                foreach ($belumDiisi as $j) {
                    $items[] = [
                        'text'  => substr($j->jamPelajaran->jam_mulai, 0, 5) . ' — ' . $j->mapel->nama . ' (' . $j->kelas->nama . ')',
                        'route' => route('guru.jurnal.create', ['jadwal_id' => $j->id]),
                    ];
                }

            } elseif ($user->hasRole('admin') || $user->hasRole('ks')) {
                $guruBelumIsi = Jadwal::select('jadwal.*')
                    ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
                    ->with(['guru', 'jamPelajaran'])
                    ->where('jam_pelajaran.hari', $hariIni)
                    ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->whereDoesntHave('jurnal', fn($q) => $q->whereDate('tanggal', today()))
                    ->get()
                    ->groupBy('guru_id');

                $count = $guruBelumIsi->count();

                foreach ($guruBelumIsi as $guruId => $jadwalGuru) {
                    $items[] = [
                        'text'  => $jadwalGuru->first()->guru->nama . ' — ' . $jadwalGuru->count() . ' sesi belum diisi',
                        'route' => null,
                    ];
                }
            }

            $view->with('notifData', [
                'count' => $count,
                'items' => $items,
                'role'  => $user->getRoleNames()->first(),
            ]);
        });
    }
}
