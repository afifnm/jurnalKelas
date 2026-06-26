<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJurnalRequest;
use App\Http\Requests\UpdateJurnalRequest;
use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\JurnalLampiran;
use App\Models\JurnalLog;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $guru = auth()->user();

        $query = Jurnal::with(['kelas', 'mapel', 'lampiran'])
            ->where('guru_id', $guru->id);

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$request->bulan]);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jurnal = $query->latest('tanggal')->paginate(15)->withQueryString();

        $hariIni = now()->dayOfWeekIso;
        $tahunAktif = TahunAjaran::aktif();
        $jadwalHariIni = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderBy('jam_mulai')
            ->get();

        $sudahDiisiHariIni = Jurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->toArray();

        $kelas = Kelas::orderBy('nama')->get();
        $mapel = Mapel::orderBy('nama')->get();

        return view('guru.jurnal.index', compact(
            'jurnal', 'jadwalHariIni', 'sudahDiisiHariIni', 'kelas', 'mapel', 'tahunAktif'
        ));
    }

    public function store(StoreJurnalRequest $request): JsonResponse
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
                $jamMulai   = strtotime($jadwal->jam_mulai);
                $jamMasuk   = strtotime($request->jam_masuk_aktual);
                $selisih    = ($jamMasuk - $jamMulai) / 60;
                if ($selisih > 0) {
                    $isTerlambat    = true;
                    $menitTerlambat = (int) $selisih;
                }
            }

            $jurnal = Jurnal::create([
                'jadwal_id'           => $request->jadwal_id,
                'guru_id'             => $guru->id,
                'kelas_id'            => $request->kelas_id,
                'mapel_id'            => $request->mapel_id,
                'tahun_ajaran_id'     => $tahunAktif?->id,
                'tanggal'             => $request->tanggal,
                'jam_masuk_aktual'    => $request->jam_masuk_aktual,
                'jam_keluar_aktual'   => $request->jam_keluar_aktual,
                'materi'              => $request->materi,
                'metode_pembelajaran' => $request->metode_pembelajaran,
                'kendala'             => $request->kendala,
                'tindak_lanjut'       => $request->tindak_lanjut,
                'catatan'             => $request->catatan,
                'status'              => 'draft',
                'is_terlambat'        => $isTerlambat,
                'menit_terlambat'     => $menitTerlambat,
            ]);

            $this->processLampiran($request, $jurnal->id);

            JurnalLog::create([
                'jurnal_id'  => $jurnal->id,
                'user_id'    => $guru->id,
                'aksi'       => 'created',
                'keterangan' => 'Jurnal dibuat.',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Jurnal berhasil disimpan.', 'jurnal' => $jurnal->load(['kelas', 'mapel'])]);
    }

    public function show(Jurnal $jurnal): JsonResponse
    {
        $this->authorize('view', $jurnal);
        return response()->json($jurnal->load(['kelas', 'mapel', 'jadwal', 'lampiran', 'log.user', 'validator']));
    }

    public function update(UpdateJurnalRequest $request, Jurnal $jurnal): JsonResponse
    {
        $jadwal = $jurnal->jadwal;

        $isTerlambat = false;
        $menitTerlambat = 0;

        if ($jadwal && $request->filled('jam_masuk_aktual')) {
            $jamMulai   = strtotime($jadwal->jam_mulai);
            $jamMasuk   = strtotime($request->jam_masuk_aktual);
            $selisih    = ($jamMasuk - $jamMulai) / 60;
            if ($selisih > 0) {
                $isTerlambat    = true;
                $menitTerlambat = (int) $selisih;
            }
        }

        DB::beginTransaction();
        try {
            $jurnal->update([
                'kelas_id'            => $request->kelas_id,
                'mapel_id'            => $request->mapel_id,
                'tanggal'             => $request->tanggal,
                'jam_masuk_aktual'    => $request->jam_masuk_aktual,
                'jam_keluar_aktual'   => $request->jam_keluar_aktual,
                'materi'              => $request->materi,
                'metode_pembelajaran' => $request->metode_pembelajaran,
                'kendala'             => $request->kendala,
                'tindak_lanjut'       => $request->tindak_lanjut,
                'catatan'             => $request->catatan,
                'is_terlambat'        => $isTerlambat,
                'menit_terlambat'     => $menitTerlambat,
            ]);

            $this->processLampiran($request, $jurnal->id);

            JurnalLog::create([
                'jurnal_id'  => $jurnal->id,
                'user_id'    => auth()->id(),
                'aksi'       => 'updated',
                'keterangan' => 'Jurnal diperbarui.',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Jurnal berhasil diperbarui.', 'jurnal' => $jurnal->fresh()->load(['kelas', 'mapel'])]);
    }

    public function destroy(Jurnal $jurnal): JsonResponse
    {
        $this->authorize('delete', $jurnal);
        $jurnal->delete();
        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }

    public function submit(Jurnal $jurnal): JsonResponse
    {
        $this->authorize('submit', $jurnal);

        $jurnal->update(['status' => 'submitted']);

        JurnalLog::create([
            'jurnal_id'  => $jurnal->id,
            'user_id'    => auth()->id(),
            'aksi'       => 'submitted',
            'keterangan' => 'Jurnal diajukan untuk validasi.',
        ]);

        return response()->json(['message' => 'Jurnal berhasil diajukan untuk validasi.']);
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
