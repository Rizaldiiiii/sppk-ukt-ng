<?php

namespace App\Http\Requests;

use App\Models\KontenPublik;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGolonganUktRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', KontenPublik::class) ?? false;
    }

    public function rules(): array
    {
        $golonganId = $this->route('golongan')?->id;

        return [
            'kode' => [
                $golonganId ? 'sometimes' : 'required',
                'string',
                'max:20',
                Rule::unique('golongan_ukt', 'kode')->ignore($golonganId),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'nominal_min' => ['required', 'integer', 'min:0'],
            'nominal_max' => ['required', 'integer', 'gte:nominal_min'],
            'sumber' => ['required', 'string', 'max:255'],
            'berlaku_mulai' => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode golongan wajib diisi.',
            'nama.required' => 'Nama golongan UKT wajib diisi.',
            'nominal_min.required' => 'Nominal batas bawah wajib diisi.',
            'nominal_max.required' => 'Nominal batas atas wajib diisi.',
            'nominal_max.gte' => 'Nominal batas atas harus lebih besar atau sama dengan nominal batas bawah.',
            'sumber.required' => 'Sumber penetapan rentang golongan wajib dicantumkan.',
            'berlaku_mulai.required' => 'Tanggal mulai berlaku wajib diisi.',
        ];
    }
}

