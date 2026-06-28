<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use App\Models\Jadwal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JamPelajaranController extends Controller
{
    public function index(): View
    {
        $slots    = JamPelajaran::orderBy('hari')->orderBy('jam_ke')->get()->groupBy('hari');
        $namaHari = Jadwal::getNamaHariList();

        return view('admin.jam-pelajaran.index', compact('slots', 'namaHari'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'hari'       => ['required', 'integer', 'min:1', 'max:7'],
            'jam_ke'     => [
                'required', 'integer', 'min:0', 'max:12',
                \Illuminate\Validation\Rule::unique('jam_pelajaran')->where(function ($query) use ($request) {
                    return $query->where('hari', $request->hari);
                })
            ],
            'jam_mulai'  => ['required', 'date_format:H:i'],
            'jam_selesai'=> ['required', 'date_format:H:i', 'after:jam_mulai'],
        ], [
            'jam_ke.unique' => 'Jam pelajaran ke-:input sudah ada pada hari ini.'
        ]);

        $overlap = JamPelajaran::where('hari', $data['hari'])
            ->where('jam_ke', '!=', $data['jam_ke'])
            ->where(function ($q) use ($data) {
                $q->where('jam_mulai', '<', $data['jam_selesai'])
                  ->where('jam_selesai', '>', $data['jam_mulai']);
            })->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => ['jam_mulai' => ['Waktu jam pelajaran bertabrakan dengan jam lain di hari yang sama.']]
            ], 422);
        }

        $slot = JamPelajaran::create([
            'hari' => $data['hari'],
            'jam_ke' => $data['jam_ke'],
            'jam_mulai' => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai']
        ]);

        return response()->json(['message' => 'Jam pelajaran berhasil disimpan.', 'slot' => $slot]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $jamPelajaran = JamPelajaran::find($id);
        
        if (!$jamPelajaran) {
            return response()->json(['message' => 'Jam pelajaran tidak ditemukan. Silakan refresh halaman.'], 404);
        }

        $data = $request->validate([
            'jam_ke'     => [
                'required', 'integer', 'min:0', 'max:12',
                \Illuminate\Validation\Rule::unique('jam_pelajaran')->where(function ($query) use ($jamPelajaran) {
                    return $query->where('hari', $jamPelajaran->hari);
                })->ignore($jamPelajaran->id)
            ],
            'jam_mulai'  => ['required', 'date_format:H:i'],
            'jam_selesai'=> ['required', 'date_format:H:i', 'after:jam_mulai'],
        ], [
            'jam_ke.unique' => 'Jam pelajaran ke-:input sudah ada pada hari ini.'
        ]);

        $overlap = JamPelajaran::where('hari', $jamPelajaran->hari)
            ->where('id', '!=', $jamPelajaran->id)
            ->where(function ($q) use ($data) {
                $q->where('jam_mulai', '<', $data['jam_selesai'])
                  ->where('jam_selesai', '>', $data['jam_mulai']);
            })->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => ['jam_mulai' => ['Waktu jam pelajaran bertabrakan dengan jam lain di hari yang sama.']]
            ], 422);
        }

        $jamPelajaran->update($data);

        return response()->json(['message' => 'Jam pelajaran berhasil diperbarui.', 'slot' => $jamPelajaran->fresh()]);
    }

    public function destroy($id): JsonResponse
    {
        $jamPelajaran = JamPelajaran::find($id);
        
        if (!$jamPelajaran) {
            return response()->json(['message' => 'Jam pelajaran tidak ditemukan atau sudah dihapus.'], 404);
        }

        $jamPelajaran->delete();

        return response()->json(['message' => 'Jam pelajaran berhasil dihapus.']);
    }

    public function cloneHari(Request $request): JsonResponse
    {
        $data = $request->validate([
            'hari_asal'    => ['required', 'integer', 'min:1', 'max:7'],
            'hari_tujuan'  => ['required', 'array', 'min:1'],
            'hari_tujuan.*'=> ['required', 'integer', 'min:1', 'max:7', 'different:hari_asal'],
        ]);

        $sumberSlots = JamPelajaran::where('hari', $data['hari_asal'])->get();

        if ($sumberSlots->isEmpty()) {
            return response()->json(['message' => 'Hari asal tidak memiliki slot jam pelajaran.'], 422);
        }

        $total = 0;
        foreach ($data['hari_tujuan'] as $hariTujuan) {
            foreach ($sumberSlots as $slot) {
                JamPelajaran::updateOrCreate(
                    ['hari' => $hariTujuan, 'jam_ke' => $slot->jam_ke],
                    ['jam_mulai' => $slot->jam_mulai, 'jam_selesai' => $slot->jam_selesai]
                );
                $total++;
            }
        }

        return response()->json([
            'message' => "{$total} slot jam pelajaran berhasil di-clone.",
        ]);
    }
}
