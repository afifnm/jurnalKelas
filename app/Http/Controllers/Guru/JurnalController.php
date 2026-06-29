<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJurnalRequest;
use App\Http\Requests\UpdateJurnalRequest;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\JurnalLampiran;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class JurnalController extends Controller
{
    public function create(Request $request): View
    {
        $guru = auth()->user();
        $hariIni = now()->dayOfWeekIso;
        $tahunAktif = TahunAjaran::aktif();

        $jadwalHariIni = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('jam_pelajaran.hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $jurnalHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->get(['id', 'jadwal_id', 'kelas_id', 'mapel_id']);

        $sudahDiisiHariIni = $jurnalHariIni->pluck('jadwal_id')->toArray();

        $selectedJadwal    = null;
        $selectedJamKeluar = '';
        if ($request->filled('jadwal_id')) {
            $selectedJadwal = $jadwalHariIni->firstWhere('id', $request->jadwal_id);
        }

        $grupJadwalHariIni = Jadwal::grupkanBerurutan($jadwalHariIni);

        if ($selectedJadwal) {
            foreach ($grupJadwalHariIni as $grup) {
                if (in_array($selectedJadwal->id, $grup['ids'])) {
                    $selectedJamKeluar = substr($grup['jadwal']->last()->jamPelajaran->jam_selesai, 0, 5);
                    break;
                }
            }
        }

        $autoFilledJadwal = null;
        $autoJamMasuk     = '';
        $autoJamKeluar    = '';

        if (! $selectedJadwal) {
            $now         = now();
            $currentTime = $now->format('H:i:s');

            foreach ($grupJadwalHariIni as $grup) {
                // Skip grup yang sudah ada jurnalnya
                if (count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0) continue;

                $firstJadwal = $grup['jadwal']->first();
                $lastJadwal  = $grup['jadwal']->last();

                $jamMulai   = \Carbon\Carbon::createFromTimeString($firstJadwal->jamPelajaran->jam_mulai);
                $jamSelesai = \Carbon\Carbon::createFromTimeString($lastJadwal->jamPelajaran->jam_selesai);

                $windowMulai = $jamMulai->copy()->subMinutes(15)->format('H:i:s');
                $windowAkhir = $jamSelesai->copy()->addMinutes(30)->format('H:i:s');

                if ($currentTime >= $windowMulai && $currentTime <= $windowAkhir) {
                    $autoFilledJadwal = $firstJadwal;
                    $autoJamMasuk  = $currentTime >= $firstJadwal->jamPelajaran->jam_mulai
                        ? $now->format('H:i')
                        : $jamMulai->format('H:i');
                    $autoJamKeluar = $jamSelesai->format('H:i');
                    break;
                }
            }
        }

        $jadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->get();

        $kelas = $jadwalGuru->pluck('kelas')->unique('id')->sortBy('nama')->values();
        $mapel = $jadwalGuru->pluck('mapel')->unique('id')->sortBy('nama')->values();

        return view('guru.jurnal.create', compact(
            'jadwalHariIni', 'sudahDiisiHariIni', 'jurnalHariIni', 'grupJadwalHariIni',
            'selectedJadwal', 'selectedJamKeluar', 'autoFilledJadwal', 'autoJamMasuk', 'autoJamKeluar',
            'kelas', 'mapel'
        ));
    }

    public function edit(Jurnal $jurnal): View
    {
        $this->authorize('update', $jurnal);
        $guru = auth()->user();
        $tahunAktif = TahunAjaran::aktif();

        $jadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->get();

        $jurnal->load(['lampiran', 'kelas', 'mapel']);

        $kelas = $jadwalGuru->pluck('kelas')->unique('id')->sortBy('nama')->values();
        $mapel = $jadwalGuru->pluck('mapel')->unique('id')->sortBy('nama')->values();

        // Pastikan kelas/mapel jurnal ini tetap tampil meski sudah tidak di jadwal aktif
        if ($jurnal->kelas && $kelas->where('id', $jurnal->kelas_id)->isEmpty()) {
            $kelas->push($jurnal->kelas);
        }
        if ($jurnal->mapel && $mapel->where('id', $jurnal->mapel_id)->isEmpty()) {
            $mapel->push($jurnal->mapel);
        }

        return view('guru.jurnal.edit', compact('jurnal', 'kelas', 'mapel'));
    }

    public function index(Request $request): View
    {
        $guru = auth()->user();

        $query = Jurnal::with(['kelas', 'mapel', 'lampiran'])
            ->where('guru_id', $guru->id);

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jurnal = $query->latest('tanggal')->paginate(15)->withQueryString();

        $hariIni = now()->dayOfWeekIso;
        $tahunAktif = TahunAjaran::aktif();
        $jadwalHariIni = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('jam_pelajaran.hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_pelajaran.jam_ke')
            ->get();

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $grupJadwalHariIni = Jadwal::grupkanBerurutan($jadwalHariIni);

        $jadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->get();

        $kelas = $jadwalGuru->pluck('kelas')->unique('id')->sortBy('nama')->values();
        $mapel = $jadwalGuru->pluck('mapel')->unique('id')->sortBy('nama')->values();

        return view('guru.jurnal.index', compact(
            'jurnal', 'jadwalHariIni', 'sudahDiisiHariIni', 'grupJadwalHariIni', 'kelas', 'mapel', 'tahunAktif'
        ));
    }

    public function store(StoreJurnalRequest $request): JsonResponse|RedirectResponse
    {
        $guru = auth()->user();
        $tahunAktif = TahunAjaran::aktif();

        DB::beginTransaction();
        try {
            $jadwal = null;
            if ($request->filled('jadwal_id')) {
                $jadwal = Jadwal::find($request->jadwal_id);
            }

            $isTerlambat = false;
            $menitTerlambat = 0;

            if ($jadwal && $request->filled('jam_masuk_aktual')) {
                $jamMulai = strtotime($jadwal->jamPelajaran->jam_mulai);
                $jamMasuk = strtotime($request->jam_masuk_aktual);
                $selisih  = ($jamMasuk - $jamMulai) / 60;
                if ($selisih > 0) {
                    $isTerlambat    = true;
                    $menitTerlambat = (int) $selisih;
                }
            }

            $jurnal = Jurnal::create([
                'jadwal_id'         => $request->jadwal_id,
                'guru_id'           => $guru->id,
                'kelas_id'          => $request->kelas_id,
                'mapel_id'          => $request->mapel_id,
                'tahun_ajaran_id'   => $tahunAktif?->id,
                'tanggal'           => $request->tanggal,
                'jam_masuk_aktual'  => $request->jam_masuk_aktual,
                'jam_keluar_aktual' => $request->jam_keluar_aktual,
                'materi'            => $request->materi,
                'catatan'           => $request->catatan,
                'is_terlambat'      => $isTerlambat,
                'menit_terlambat'   => $menitTerlambat,
            ]);

            $this->processLampiran($request, $jurnal->id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()], 500);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Jurnal berhasil disimpan.', 'jurnal' => $jurnal->load(['kelas', 'mapel'])]);
        }

        return redirect()->route('guru.jurnal.index')->with('success', 'Jurnal berhasil disimpan.');
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        $this->authorize('view', $jurnal);
        return response()->json($jurnal->load(['kelas', 'mapel', 'jadwal', 'lampiran']));
    }

    public function update(UpdateJurnalRequest $request, Jurnal $jurnal): JsonResponse|RedirectResponse
    {
        $jadwal = $jurnal->jadwal;

        $isTerlambat = false;
        $menitTerlambat = 0;

        if ($jadwal && $request->filled('jam_masuk_aktual')) {
            $jamMulai = strtotime($jadwal->jamPelajaran->jam_mulai);
            $jamMasuk = strtotime($request->jam_masuk_aktual);
            $selisih  = ($jamMasuk - $jamMulai) / 60;
            if ($selisih > 0) {
                $isTerlambat    = true;
                $menitTerlambat = (int) $selisih;
            }
        }

        DB::beginTransaction();
        try {
            $jurnal->update([
                'kelas_id'          => $request->kelas_id,
                'mapel_id'          => $request->mapel_id,
                'tanggal'           => $request->tanggal,
                'jam_masuk_aktual'  => $request->jam_masuk_aktual,
                'jam_keluar_aktual' => $request->jam_keluar_aktual,
                'materi'            => $request->materi,
                'catatan'           => $request->catatan,
                'is_terlambat'      => $isTerlambat,
                'menit_terlambat'   => $menitTerlambat,
            ]);

            $this->processLampiran($request, $jurnal->id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Jurnal berhasil diperbarui.', 'jurnal' => $jurnal->fresh()->load(['kelas', 'mapel'])]);
        }

        return redirect()->route('guru.jurnal.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy(Jurnal $jurnal): JsonResponse
    {
        $this->authorize('delete', $jurnal);
        $jurnal->delete();
        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }

    public function hapusLampiran(JurnalLampiran $lampiran): JsonResponse
    {
        $this->authorize('update', $lampiran->jurnal);
        Storage::disk('local')->delete($lampiran->path);
        $lampiran->delete();
        return response()->json(['message' => 'Lampiran berhasil dihapus.']);
    }

    private function processLampiran(Request $request, int $jurnalId): void
    {
        if (! $request->hasFile('lampiran')) {
            return;
        }

        foreach ($request->file('lampiran') as $i => $file) {
            $path = 'lampiran/' . date('Y/m') . '/' . uniqid() . '.webp';

            $img = Image::read($file)
                ->scaleDown(1200, 1200)
                ->toWebp(85);

            Storage::disk('local')->put($path, $img);

            JurnalLampiran::create([
                'jurnal_id'  => $jurnalId,
                'path'       => $path,
                'keterangan' => $request->keterangan_lampiran[$i] ?? null,
            ]);
        }
    }
}
