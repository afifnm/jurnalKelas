<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email'     => ['nullable', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,guru,ks'],
            'no_hp'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }
}
