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
            ->orderBy('jam_pelajaran.jam_mulai')
            ->get();

        $jurnalHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->get(['id', 'jadwal_id', 'kelas_id', 'mapel_id']);

        $sudahDiisiHariIni = $jurnalHariIni->pluck('jadwal_id')->toArray();

        $selectedJadwal = null;
        if ($request->filled('jadwal_id')) {
            $selectedJadwal = $jadwalHariIni->firstWhere('id', $request->jadwal_id);
        }

        $grupJadwalHariIni = Jadwal::grupkanBerurutan($jadwalHariIni);

        $autoFilledJadwal = null;

        if (! $selectedJadwal) {
            $currentTime = now()->format('H:i:s');

            foreach ($grupJadwalHariIni as $grup) {
                if (count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0) continue;

                $firstJadwal = $grup['jadwal']->first();
                $lastJadwal  = $grup['jadwal']->last();

                $jamMulai   = \Carbon\Carbon::createFromTimeString($firstJadwal->jamPelajaran->jam_mulai);
                $jamSelesai = \Carbon\Carbon::createFromTimeString($lastJadwal->jamPelajaran->jam_selesai);

                $windowMulai = $jamMulai->copy()->subMinutes(15)->format('H:i:s');
                $windowAkhir = $jamSelesai->copy()->addMinutes(30)->format('H:i:s');

                if ($currentTime >= $windowMulai && $currentTime <= $windowAkhir) {
                    $autoFilledJadwal = $firstJadwal;
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
            'selectedJadwal', 'autoFilledJadwal',
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

        $jurnal->load(['lampiran', 'kelas', 'mapel', 'jadwal.jamPelajaran']);

        $kelas = $jadwalGuru->pluck('kelas')->unique('id')->sortBy('nama')->values();
        $mapel = $jadwalGuru->pluck('mapel')->unique('id')->sortBy('nama')->values();

        if ($jurnal->kelas && $kelas->where('id', $jurnal->kelas_id)->isEmpty()) {
            $kelas->push($jurnal->kelas);
        }
        if ($jurnal->mapel && $mapel->where('id', $jurnal->mapel_id)->isEmpty()) {
            $mapel->push($jurnal->mapel);
        }

        $jamSesiMap = Jurnal::buildJamSesiMap(collect([$jurnal]));
        $jamSesi    = $jamSesiMap[$jurnal->id] ?? null;

        return view('guru.jurnal.edit', compact('jurnal', 'kelas', 'mapel', 'jamSesi'));
    }

    public function index(Request $request): View
    {
        $guru = auth()->user();
        $tahunAktif = TahunAjaran::aktif();

        $query = Jurnal::with(['guru', 'kelas', 'mapel', 'lampiran', 'jadwal.jamPelajaran'])
            ->where('guru_id', $guru->id);

        $periode = $request->input('periode');
        if ($periode === 'hari_ini') {
            $query->whereDate('tanggal', today());
        } elseif ($periode === 'minggu_ini') {
            $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        } elseif ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            if ($request->filled('tanggal_dari')) $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            if ($request->filled('tanggal_sampai')) $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jurnal = $query->latest('tanggal')->paginate(20)->withQueryString();

        $hariIni = now()->dayOfWeekIso;
        $jadwalHariIni = Jadwal::select('jadwal.*')
            ->join('jam_pelajaran', 'jadwal.jam_pelajaran_id', '=', 'jam_pelajaran.id')
            ->with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('jam_pelajaran.hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_pelajaran.jam_mulai')
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

        $kelas    = $jadwalGuru->pluck('kelas')->unique('id')->sortBy('nama')->values();
        $jamSesiMap = Jurnal::buildJamSesiMap($jurnal);

        return view('guru.jurnal.index', [
            'jurnal'            => $jurnal,
            'jamSesiMap'        => $jamSesiMap,
            'jadwalHariIni'     => $jadwalHariIni,
            'sudahDiisiHariIni' => $sudahDiisiHariIni,
            'grupJadwalHariIni' => $grupJadwalHariIni,
            'kelas'             => $kelas,
            'guru'              => collect(),
            'tahunAktif'        => $tahunAktif,
            'canCreate'         => true,
            'canEdit'           => true,
            'breadcrumbRole'    => 'Guru',
            'headerDesc'        => 'Riwayat dan pengisian jurnal harian',
            'indexRoute'        => route('guru.jurnal.index'),
            'showRouteBase'     => '/guru/jurnal',
            'showRouteSuffix'   => '/show',
        ]);
    }

    public function store(StoreJurnalRequest $request): JsonResponse|RedirectResponse
    {
        $guru = auth()->user();
        $tahunAktif = TahunAjaran::aktif();

        DB::beginTransaction();
        try {
            $jurnal = Jurnal::create([
                'jadwal_id'       => $request->jadwal_id,
                'guru_id'         => $guru->id,
                'kelas_id'        => $request->kelas_id,
                'mapel_id'        => $request->mapel_id,
                'tahun_ajaran_id' => $tahunAktif?->id,
                'tanggal'         => $request->tanggal,
                'materi'          => $request->materi,
                'catatan'         => $request->catatan,
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
        $jurnal->load(['guru', 'kelas', 'mapel', 'jadwal.jamPelajaran', 'lampiran']);
        $sesi = Jurnal::buildJamSesiMap(collect([$jurnal]));
        $data = $jurnal->toArray();
        $data['jam_sesi'] = $sesi[$jurnal->id] ?? null;
        $data['dalam_jam'] = $jurnal->isInputDalamJamMengajar();
        return response()->json($data);
    }

    public function update(UpdateJurnalRequest $request, Jurnal $jurnal): JsonResponse|RedirectResponse
    {
        DB::beginTransaction();
        try {
            $jurnal->update([
                'materi'  => $request->materi,
                'catatan' => $request->catatan,
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
                ->scaleDown(800, 800)
                ->toWebp(60);

            Storage::disk('local')->put($path, $img);

            JurnalLampiran::create([
                'jurnal_id'  => $jurnalId,
                'path'       => $path,
                'keterangan' => $request->keterangan_lampiran[$i] ?? null,
            ]);
        }
    }
}
