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
            'jam_pelajaran_id'=> ['required', 'exists:jam_pelajaran,id'],
        ];
    }
}
