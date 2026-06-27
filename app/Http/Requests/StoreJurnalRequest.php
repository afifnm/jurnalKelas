<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJurnalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('guru');
    }

    public function rules(): array
    {
        return [
            'jadwal_id'             => ['nullable', 'exists:jadwal,id'],
            'kelas_id'              => ['required', 'exists:kelas,id'],
            'mapel_id'              => ['required', 'exists:mapel,id'],
            'tanggal'               => ['required', 'date', 'before_or_equal:today'],
            'jam_masuk_aktual'      => ['required', 'date_format:H:i'],
            'jam_keluar_aktual'     => ['nullable', 'date_format:H:i', 'after:jam_masuk_aktual'],
            'materi'                => ['required', 'string', 'max:2000'],
            'catatan'               => ['nullable', 'string', 'max:1000'],
            'lampiran'              => ['nullable', 'array', 'max:5'],
            'lampiran.*'            => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'keterangan_lampiran'   => ['nullable', 'array'],
            'keterangan_lampiran.*' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.before_or_equal' => 'Tanggal jurnal tidak boleh lebih dari hari ini.',
            'jam_masuk_aktual.required' => 'Jam masuk aktual wajib diisi.',
            'materi.required' => 'Materi pembelajaran wajib diisi.',
        ];
    }
}
