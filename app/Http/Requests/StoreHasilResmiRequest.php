<?php

namespace App\Http\Requests;

use App\Models\HasilUkt;
use Illuminate\Foundation\Http\FormRequest;

class StoreHasilResmiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage', HasilUkt::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'mahasiswa_id' => ['required', 'integer', 'exists:mahasiswa,id'],
            'golongan_id' => ['required', 'integer', 'exists:golongan_ukt,id'],
            'nominal' => ['required', 'integer', 'min:0'],
            'tanggal_penetapan' => ['required', 'date'],
            'sumber_penetapan' => ['required', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'masukan' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'mahasiswa_id.required' => 'Mahasiswa wajib dipilih.',
            'golongan_id.required' => 'Golongan UKT wajib dipilih.',
            'nominal.required' => 'Nominal penetapan resmi wajib diisi.',
            'tanggal_penetapan.required' => 'Tanggal penetapan wajib diisi.',
            'sumber_penetapan.required' => 'Sumber SK / penetapan resmi kampus wajib dicantumkan.',
            'masukan.required' => 'Rincian masukan komponen wajib diisi agar alasan dapat ditelusuri.',
        ];
    }
}

