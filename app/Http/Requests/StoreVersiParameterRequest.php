<?php

namespace App\Http\Requests;

use App\Models\VersiParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVersiParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', VersiParameter::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'versi' => ['required', 'string', 'max:40', Rule::unique('versi_parameter', 'versi')],
            'berlaku_mulai' => ['required', 'date'],
            'catatan' => ['required', 'string', 'max:1000'],
            'bobot' => ['required', 'array'],
            'bobot.*' => ['required', 'numeric', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'versi.required' => 'Kode versi parameter baru wajib diisi (misalnya v1.1).',
            'versi.unique' => 'Kode versi parameter tersebut sudah pernah digunakan. Versi lama tidak boleh ditimpa.',
            'berlaku_mulai.required' => 'Tanggal mulai berlaku versi parameter wajib diisi.',
            'catatan.required' => 'Catatan alasan pembuatan versi parameter baru wajib diisi.',
        ];
    }
}

