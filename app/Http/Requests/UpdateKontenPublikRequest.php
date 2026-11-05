<?php

namespace App\Http\Requests;

use App\Models\KontenPublik;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKontenPublikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', KontenPublik::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:1000'],
            'isi' => ['required', 'string', 'max:50000'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul halaman publik wajib diisi.',
            'isi.required' => 'Isi halaman publik wajib diisi.',
        ];
    }
}

