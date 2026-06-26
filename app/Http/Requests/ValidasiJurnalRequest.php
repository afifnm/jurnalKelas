<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidasiJurnalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['ks', 'admin']);
    }

    public function rules(): array
    {
        return [
            'aksi'             => ['required', 'in:validated,revisi'],
            'catatan_validasi' => ['required_if:aksi,revisi', 'nullable', 'string', 'max:1000'],
        ];
    }
}
