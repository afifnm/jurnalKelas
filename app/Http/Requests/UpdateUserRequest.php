<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        return [
            'nama'      => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', "unique:users,username,{$userId}", 'alpha_dash'],
            'email'     => ['nullable', 'email', "unique:users,email,{$userId}"],
            'password'  => ['nullable', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,guru,ks'],
            'no_hp'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }
}
