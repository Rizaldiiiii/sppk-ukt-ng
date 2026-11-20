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
        $slug = (string) $this->route('slug');

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:1000'],
        ];

        if ($slug === 'alur') {
            if ($this->filled('isi') && ! $this->has('tahapan')) {
                $rules['isi'] = ['required', 'string', 'max:50000'];
            } else {
                $rules['tahapan'] = ['required', 'array', 'min:1'];
                $rules['tahapan.*.judul'] = ['required', 'string', 'max:255'];
                $rules['tahapan.*.deskripsi'] = ['required', 'string', 'max:5000'];
                $rules['tahapan.*.hubungan_data'] = ['nullable', 'string', 'max:5000'];
            }

            return $rules;
        }

        if ($slug === 'prosedur-keberatan') {
            if (
                $this->filled('isi')
                && ! $this->hasAny(['tujuan_pengajuan', 'siapa_boleh_mengajukan', 'dokumen_diperlukan', 'tahapan_peninjauan'])
            ) {
                $rules['isi'] = ['required', 'string', 'max:50000'];
            } else {
                $rules['tujuan_pengajuan'] = ['required', 'string', 'max:5000'];
                $rules['siapa_boleh_mengajukan'] = ['required', 'array', 'min:1'];
                $rules['siapa_boleh_mengajukan.*'] = ['required', 'string', 'max:2000'];
                $rules['dokumen_diperlukan'] = ['required', 'array', 'min:1'];
                $rules['dokumen_diperlukan.*'] = ['required', 'string', 'max:2000'];
                $rules['tahapan_peninjauan'] = ['required', 'array', 'min:1'];
                $rules['tahapan_peninjauan.*'] = ['required', 'string', 'max:2000'];
            }

            return $rules;
        }

        if ($slug === 'petunjuk-pengisian') {
            if ($this->filled('isi') && ! $this->has('petunjuk')) {
                $rules['isi'] = ['required', 'string', 'max:50000'];
            } else {
                $rules['petunjuk'] = ['required', 'array', 'min:1'];
                $rules['petunjuk.*.judul'] = ['required', 'string', 'max:255'];
                $rules['petunjuk.*.apa_diisi'] = ['required', 'string', 'max:2000'];
                $rules['petunjuk.*.format'] = ['required', 'string', 'max:2000'];
                $rules['petunjuk.*.contoh'] = ['required', 'string', 'max:2000'];
                $rules['petunjuk.*.akibat'] = ['required', 'string', 'max:2000'];
            }

            return $rules;
        }

        if ($slug === 'panduan') {
            $rules['isi'] = ['nullable', 'string', 'max:50000'];

            return $rules;
        }

        $rules['isi'] = ['required', 'string', 'max:50000'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul halaman publik wajib diisi.',
            'isi.required' => 'Isi halaman publik wajib diisi.',
            'tahapan.required' => 'Minimal harus ada 1 tahapan pada alur penentuan UKT.',
            'tahapan.array' => 'Format tahapan alur tidak valid.',
            'tahapan.min' => 'Minimal harus ada 1 tahapan pada alur penentuan UKT.',
            'tahapan.*.judul.required' => 'Judul tahapan wajib diisi.',
            'tahapan.*.deskripsi.required' => 'Deskripsi tahapan wajib diisi.',
            'tujuan_pengajuan.required' => 'Tujuan pengajuan keberatan wajib diisi.',
            'siapa_boleh_mengajukan.required' => 'Daftar siapa yang boleh mengajukan wajib memiliki minimal 1 butir.',
            'siapa_boleh_mengajukan.min' => 'Daftar siapa yang boleh mengajukan wajib memiliki minimal 1 butir.',
            'siapa_boleh_mengajukan.*.required' => 'Setiap butir syarat pengajuan wajib diisi.',
            'dokumen_diperlukan.required' => 'Daftar dokumen diperlukan wajib memiliki minimal 1 butir.',
            'dokumen_diperlukan.min' => 'Daftar dokumen diperlukan wajib memiliki minimal 1 butir.',
            'dokumen_diperlukan.*.required' => 'Setiap butir dokumen diperlukan wajib diisi.',
            'tahapan_peninjauan.required' => 'Daftar tahapan peninjauan wajib memiliki minimal 1 butir.',
            'tahapan_peninjauan.min' => 'Daftar tahapan peninjauan wajib memiliki minimal 1 butir.',
            'tahapan_peninjauan.*.required' => 'Setiap butir tahapan peninjauan wajib diisi.',
            'petunjuk.required' => 'Data petunjuk pengisian wajib diisi.',
            'petunjuk.*.judul.required' => 'Judul kolom petunjuk pengisian wajib diisi.',
            'petunjuk.*.apa_diisi.required' => 'Penjelasan apa yang diisi wajib diisi.',
            'petunjuk.*.format.required' => 'Format pengisian wajib diisi.',
            'petunjuk.*.contoh.required' => 'Contoh pengisian wajib diisi.',
            'petunjuk.*.akibat.required' => 'Penjelasan akibat bila keliru wajib diisi.',
        ];
    }
}

