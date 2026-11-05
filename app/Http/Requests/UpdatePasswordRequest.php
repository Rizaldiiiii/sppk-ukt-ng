<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'password_lama' => ['required', 'string', 'current_password'],
            'password_baru' => ['required', 'string', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'password_lama.required' => 'Kata sandi saat ini wajib diisi.',
            'password_lama.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password_baru.required' => 'Kata sandi baru wajib diisi.',
            'password_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password_baru.min' => 'Kata sandi baru minimal 8 karakter.',
        ];
    }
}

