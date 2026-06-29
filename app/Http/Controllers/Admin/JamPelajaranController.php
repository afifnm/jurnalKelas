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
        $slots    = JamPelajaran::orderBy('hari')->orderBy('jam_mulai')->get()->groupBy('hari');
        $namaHari = Jadwal::getNamaHariList();

        return view('admin.jam-pelajaran.index', compact('slots', 'namaHari'));
    }

    public function store(Request $request): JsonResponse
    {
        $isIstirahat = (bool) $request->input('is_istirahat', false);

        $jamKeRules = $isIstirahat
            ? ['nullable', 'integer']
            : [
                'required', 'integer', 'min:0', 'max:12',
                \Illuminate\Validation\Rule::unique('jam_pelajaran')->where(fn($q) => $q->where('hari', $request->hari)),
              ];

        $data = $request->validate([
            'hari'         => ['required', 'integer', 'min:1', 'max:7'],
            'jam_ke'       => $jamKeRules,
            'jam_mulai'    => ['required', 'date_format:H:i'],
            'jam_selesai'  => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'is_istirahat' => ['boolean'],
        ], [
            'jam_ke.unique' => 'Jam pelajaran ke-:input sudah ada pada hari ini.'
        ]);

        if ($isIstirahat) {
            // Auto-assign jam_ke untuk istirahat: mulai dari 101 agar tidak konflik dengan jam normal (0-12)
            $maxIstirahat = JamPelajaran::where('hari', $data['hari'])
                ->where('is_istirahat', true)
                ->max('jam_ke') ?? 100;
            $data['jam_ke'] = $maxIstirahat + 1;
        } else {
            $overlap = JamPelajaran::where('hari', $data['hari'])
                ->where('jam_ke', '!=', $data['jam_ke'])
                ->where('is_istirahat', false)
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
        }

        $slot = JamPelajaran::create([
            'hari'         => $data['hari'],
            'jam_ke'       => $data['jam_ke'],
            'jam_mulai'    => $data['jam_mulai'],
            'jam_selesai'  => $data['jam_selesai'],
            'is_istirahat' => $isIstirahat,
        ]);

        return response()->json(['message' => 'Jam pelajaran berhasil disimpan.', 'slot' => $slot]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $jamPelajaran = JamPelajaran::find($id);
        
        if (!$jamPelajaran) {
            return response()->json(['message' => 'Jam pelajaran tidak ditemukan. Silakan refresh halaman.'], 404);
        }

        $isIstirahat = (bool) $request->input('is_istirahat', $jamPelajaran->is_istirahat);

        $jamKeRules = $isIstirahat
            ? ['nullable', 'integer']
            : [
                'required', 'integer', 'min:0', 'max:12',
                \Illuminate\Validation\Rule::unique('jam_pelajaran')->where(fn($q) => $q->where('hari', $jamPelajaran->hari))->ignore($jamPelajaran->id),
              ];

        $data = $request->validate([
            'jam_ke'       => $jamKeRules,
            'jam_mulai'    => ['required', 'date_format:H:i'],
            'jam_selesai'  => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'is_istirahat' => ['boolean'],
        ], [
            'jam_ke.unique' => 'Jam pelajaran ke-:input sudah ada pada hari ini.'
        ]);

        if (!$isIstirahat) {
            $overlap = JamPelajaran::where('hari', $jamPelajaran->hari)
                ->where('id', '!=', $jamPelajaran->id)
                ->where('is_istirahat', false)
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
        }

        $data['is_istirahat'] = $isIstirahat;
        if ($isIstirahat) {
            $data['jam_ke'] = $jamPelajaran->jam_ke; // pertahankan jam_ke lama untuk istirahat
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
                    ['jam_mulai' => $slot->jam_mulai, 'jam_selesai' => $slot->jam_selesai, 'is_istirahat' => $slot->is_istirahat]
                );
                $total++;
            }
        }

        return response()->json([
            'message' => "{$total} slot jam pelajaran berhasil di-clone.",
        ]);
    }
}
