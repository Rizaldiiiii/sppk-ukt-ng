<?php

namespace App\Http\Requests;

use App\Models\KontenPublik;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFaktorPenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', KontenPublik::class) ?? false;
    }

    public function rules(): array
    {
        $faktorId = $this->route('faktor')?->id;

        return [
            'kode' => [
                $faktorId ? 'sometimes' : 'required',
                'string',
                'max:60',
                'alpha_dash',
                Rule::unique('faktor_penilaian', 'kode')->ignore($faktorId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'penjelasan_singkat' => ['required', 'string', 'max:2000'],
            'pengaruh_umum' => ['nullable', 'string', 'max:2000'],
            'satuan' => ['nullable', 'string', 'max:40'],
            'status_konfirmasi' => ['required', Rule::in(['terkonfirmasi', 'menunggu_konfirmasi'])],
            'urutan' => ['required', 'integer', 'min:1', 'max:999'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode faktor penilaian wajib diisi.',
            'kode.unique' => 'Kode faktor tersebut sudah terdaftar.',
            'nama.required' => 'Nama faktor penilaian wajib diisi.',
            'penjelasan_singkat.required' => 'Penjelasan singkat faktor wajib diisi.',
            'status_konfirmasi.required' => 'Status konfirmasi resmi wajib dipilih.',
            'status_konfirmasi.in' => 'Status konfirmasi hanya boleh "terkonfirmasi" atau "menunggu_konfirmasi".',
        ];
    }
}

