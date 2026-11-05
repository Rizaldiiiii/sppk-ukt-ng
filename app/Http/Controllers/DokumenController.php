<?php

namespace App\Http\Controllers;

use App\Models\DokumenKeberatan;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    /**
     * Akses dokumen keberatan dari storage privat melalui pemeriksaan DokumenPolicy (Blueprint §9 & §15).
     */
    public function unduh(Request $request, DokumenKeberatan $dokumen): StreamedResponse
    {
        $dokumen->loadMissing('pengajuan');

        Gate::authorize('view', $dokumen);

        if (! Storage::disk('local')->exists($dokumen->path_penyimpanan)) {
            abort(404, 'Berkas dokumen tidak ditemukan di penyimpanan privat.');
        }

        $this->auditService->catat(
            pelaku: $request->user(),
            aksi: 'akses_dokumen_keberatan',
            subjekTipe: DokumenKeberatan::class,
            subjekId: $dokumen->id,
            nilaiSebelum: null,
            nilaiSesudah: [
                'pengajuan_id' => $dokumen->pengajuan_id,
                'nama_asli' => $dokumen->nama_asli,
            ]
        );

        return Storage::disk('local')->download(
            $dokumen->path_penyimpanan,
            $dokumen->nama_asli,
            ['Content-Type' => $dokumen->mime]
        );
    }
}

