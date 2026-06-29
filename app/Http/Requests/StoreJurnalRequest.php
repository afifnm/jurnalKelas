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
            'tanggal'               => ['required', 'date', 'date_equals:today'],
            'materi'                => ['required', 'string', 'max:2000'],
            'catatan'               => ['nullable', 'string', 'max:1000'],
            'lampiran'              => ['nullable', 'array', 'max:5'],
            'lampiran.*'            => ['image', 'mimes:jpg,jpeg,png,webp'],
            'keterangan_lampiran'   => ['nullable', 'array'],
            'keterangan_lampiran.*' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.date_equals' => 'Jurnal hanya bisa diisi untuk hari ini.',
            'materi.required'     => 'Materi pembelajaran wajib diisi.',
            'lampiran.*.uploaded' => 'Gagal mengupload foto. Mungkin file terlalu besar untuk diterima oleh server atau format tidak didukung.',
        ];
    }
}
