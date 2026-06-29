<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJurnalRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jurnal = $this->route('jurnal');
        return $this->user()->hasRole('guru')
            && $jurnal->guru_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
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
            'lampiran.*.uploaded' => 'Gagal mengupload foto. Mungkin file terlalu besar untuk diterima oleh server atau format tidak didukung.',
        ];
    }
}
