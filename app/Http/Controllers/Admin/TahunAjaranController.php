<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        $tahunAjaran    = TahunAjaran::withCount('jadwal')->orderBy('nama')->paginate(20);
        $tahunAjaranAll = TahunAjaran::withCount('jadwal')->orderBy('nama')->get();
        return view('admin.tahun-ajaran.index', compact('tahunAjaran', 'tahunAjaranAll'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['boolean'],
        ]);

        if ($request->boolean('is_aktif')) {
            TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        $ta = TahunAjaran::create([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return response()->json(['message' => 'Tahun ajaran berhasil ditambahkan.', 'data' => $ta]);
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['boolean'],
        ]);

        if ($request->boolean('is_aktif')) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_aktif' => false]);
        }

        $tahunAjaran->update([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return response()->json(['message' => 'Tahun ajaran berhasil diperbarui.', 'data' => $tahunAjaran]);
    }

    public function destroy(TahunAjaran $tahunAjaran): JsonResponse
    {
        if ($tahunAjaran->is_aktif) {
            return response()->json(['message' => 'Tahun ajaran aktif tidak bisa dihapus.'], 422);
        }
        $tahunAjaran->jadwal()->delete();
        $tahunAjaran->delete();
        return response()->json(['message' => 'Tahun ajaran berhasil dihapus.']);
    }

    public function aktivasi(TahunAjaran $tahunAjaran): JsonResponse
    {
        TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        $tahunAjaran->update(['is_aktif' => true]);
        return response()->json(['message' => "Tahun ajaran {$tahunAjaran->nama} ({$tahunAjaran->semester}) diaktifkan."]);
    }

    public function cloneJadwal(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $request->validate([
            'source_id' => ['required', 'integer', 'exists:tahun_ajaran,id'],
        ]);

        $source = TahunAjaran::findOrFail($request->source_id);

        $tahunAjaran->jadwal()->forceDelete();

        $jadwalData = [];
        $now = now();
        foreach ($source->jadwal as $j) {
            $jadwalData[] = [
                'guru_id'          => $j->guru_id,
                'kelas_id'         => $j->kelas_id,
                'mapel_id'         => $j->mapel_id,
                'tahun_ajaran_id'  => $tahunAjaran->id,
                'jam_pelajaran_id' => $j->jam_pelajaran_id,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if (!empty($jadwalData)) {
            Jadwal::insert($jadwalData);
        }

        $count = $source->jadwal->count();
        return response()->json([
            'message' => "Berhasil menyalin {$count} jadwal dari {$source->label} ke {$tahunAjaran->label}.",
        ]);
    }

    public function generateJadwal(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $tahunAjaran->jadwal()->forceDelete();

        $tugasMengajars = \App\Models\TugasMengajar::where('tahun_ajaran_id', $tahunAjaran->id)->get();
        $jamPelajarans  = \App\Models\JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get();

        if ($tugasMengajars->isEmpty() || $jamPelajarans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada tugas mengajar atau jam pelajaran yang tersedia.', 'gagal' => 0]);
        }

        // $slotIds[$hari][$jam_ke] = jam_pelajaran_id
        // $hariSlots[$hari] = [jam_ke, ...] berurutan
        $slotIds   = [];
        $hariSlots = [];
        foreach ($jamPelajarans as $jp) {
            $slotIds[$jp->hari][$jp->jam_ke] = $jp->id;
            $hariSlots[$jp->hari][]          = $jp->jam_ke;
        }
        $hariList = array_keys($hariSlots);

        // MRV: tugas paling ketat (jumlah_jam besar) dikerjakan duluan
        $sorted = $tugasMengajars->sortByDesc('jumlah_jam')->values();

        $opsi      = $request->input('opsi', 'all_at_once'); // 'max_4' | 'all_at_once'
        // Maksimal jam berturut-turut per hari untuk opsi pemisahan (default 4, rentang 1–10)
        $maxJam    = (int) $request->input('max_jam', 4);
        if ($maxJam < 1)  $maxJam = 1;
        if ($maxJam > 10) $maxJam = 10;
        $busyGuru  = []; // "{guru_id}:{hari}:{jam_ke}" = true
        $busyKelas = []; // "{kelas_id}:{hari}:{jam_ke}" = true

        $jadwalInserts = [];
        $now           = now();
        $gagalHitung   = 0;

        // Cari N slot consecutive di satu hari yang bebas untuk guru & kelas.
        $findConsecutive = function (int $hari, int $n, int $guruId, int $kelasId) use ($hariSlots, &$busyGuru, &$busyKelas): ?array {
            $consecutive = [];
            foreach ($hariSlots[$hari] as $jam_ke) {
                if (isset($busyGuru["{$guruId}:{$hari}:{$jam_ke}"]) || isset($busyKelas["{$kelasId}:{$hari}:{$jam_ke}"])) {
                    $consecutive = [];
                    continue;
                }
                $consecutive[] = $jam_ke;
                if (count($consecutive) === $n) return $consecutive;
            }
            return null;
        };

        // Tandai slot dan tambahkan ke jadwalInserts.
        $placeSlots = function (int $hari, array $slots, object $tugas) use (&$busyGuru, &$busyKelas, &$jadwalInserts, $slotIds, $tahunAjaran, $now): void {
            foreach ($slots as $jam_ke) {
                $busyGuru["{$tugas->guru_id}:{$hari}:{$jam_ke}"]   = true;
                $busyKelas["{$tugas->kelas_id}:{$hari}:{$jam_ke}"] = true;
                $jadwalInserts[] = [
                    'guru_id'          => $tugas->guru_id,
                    'kelas_id'         => $tugas->kelas_id,
                    'mapel_id'         => $tugas->mapel_id,
                    'tahun_ajaran_id'  => $tahunAjaran->id,
                    'jam_pelajaran_id' => $slotIds[$hari][$jam_ke],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        };

        foreach ($sorted as $tugas) {
            $total = (int) $tugas->jumlah_jam;
            if ($total <= 0) continue;

            if ($opsi === 'all_at_once') {
                // Semua JP harus consecutive dalam 1 hari, tidak boleh dipecah
                $placed = false;
                foreach ($hariList as $hari) {
                    $slots = $findConsecutive($hari, $total, $tugas->guru_id, $tugas->kelas_id);
                    if ($slots === null) continue;
                    $placeSlots($hari, $slots, $tugas);
                    $placed = true;
                    break;
                }
                if (!$placed) $gagalHitung++;
            } else {
                // pecah jadi chunk ≤ $maxJam, tiap chunk di hari berbeda (preferensi)
                $chunks = [];
                $sisa   = $total;
                while ($sisa > 0) {
                    $chunks[] = min($maxJam, $sisa);
                    $sisa    -= min($maxJam, $sisa);
                }

                $hariTerpakai = [];
                foreach ($chunks as $chunk) {
                    $placed = false;

                    // Utamakan hari yang belum dipakai tugas ini
                    foreach ($hariList as $hari) {
                        if (in_array($hari, $hariTerpakai, true)) continue;
                        $slots = $findConsecutive($hari, $chunk, $tugas->guru_id, $tugas->kelas_id);
                        if ($slots === null) continue;
                        $placeSlots($hari, $slots, $tugas);
                        $hariTerpakai[] = $hari;
                        $placed         = true;
                        break;
                    }

                    // Fallback: boleh hari yang sudah dipakai jika tidak ada pilihan lain
                    if (!$placed) {
                        foreach ($hariList as $hari) {
                            $slots = $findConsecutive($hari, $chunk, $tugas->guru_id, $tugas->kelas_id);
                            if ($slots === null) continue;
                            $placeSlots($hari, $slots, $tugas);
                            $hariTerpakai[] = $hari;
                            $placed         = true;
                            break;
                        }
                    }

                    if (!$placed) $gagalHitung++;
                }
            }
        }

        foreach (array_chunk($jadwalInserts, 500) as $batch) {
            Jadwal::insert($batch);
        }

        $berhasil = count($jadwalInserts);
        $message  = "Berhasil menyusun {$berhasil} jadwal.";
        if ($gagalHitung > 0) {
            $message .= " Namun {$gagalHitung} chunk jam tidak dapat dijadwalkan karena tidak ada slot consecutive yang tersedia.";
        }

        return response()->json(['message' => $message, 'gagal' => $gagalHitung]);
    }
}
