<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'guru_id'         => ['required', 'exists:users,id'],
            'kelas_id'        => ['required', 'exists:kelas,id'],
            'mapel_id'        => ['required', 'exists:mapel,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'hari'            => ['required', 'integer', 'min:1', 'max:7'],
            'jam_mulai'       => ['required', 'date_format:H:i'],
            'jam_selesai'     => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ];
    }
}
