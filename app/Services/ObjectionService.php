<?php

namespace App\Services;

use App\Models\DokumenKeberatan;
use App\Models\Mahasiswa;
use App\Models\PengajuanKeberatan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class ObjectionService
{
    public const STATUS_DIAJUKAN = 'Diajukan';
    public const STATUS_DALAM_PENINJAUAN = 'Dalam Peninjauan';
    public const STATUS_PERLU_PERBAIKAN = 'Perlu Perbaikan';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DITOLAK = 'Ditolak';

    /**
     * Batas konservatif sementara untuk unggahan dokumen bukti keberatan
     * (PRD Bagian 9 butir 8 & Blueprint §9 — menunggu konfirmasi resmi kampus).
     */
    public const MAX_FILES = 3;
    public const MAX_FILE_SIZE_KB = 2048; // 2 MB per berkas
    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];
    public const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];

    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
        private readonly SlaService $slaService,
    ) {}

    /**
     * Daftar seluruh status usulan beserta penanda status terminal (Blueprint §7.3).
     *
     * @return list<string>
     */
    public static function daftarStatus(): array
    {
        return [
            self::STATUS_DIAJUKAN,
            self::STATUS_DALAM_PENINJAUAN,
            self::STATUS_PERLU_PERBAIKAN,
            self::STATUS_DISETUJUI,
            self::STATUS_DITOLAK,
        ];
    }

    public static function apakahStatusTerminal(string $status): bool
    {
        return in_array($status, [self::STATUS_DISETUJUI, self::STATUS_DITOLAK], true);
    }

    /**
     * Simpan pengajuan keberatan baru beserta unggahan dokumen bukti privat dalam satu transaksi.
     * Bila transaksi gagal, berkas yang terlanjur ditulis ke disk privat dihapus kembali (Blueprint §7.3 & §9).
     *
     * @param  array<int, UploadedFile>  $berkasUnggahan
     */
    public function ajukan(
        Mahasiswa $mahasiswa,
        User $pengunggah,
        ?int $hasilId,
        string $alasan,
        array $berkasUnggahan = []
    ): PengajuanKeberatan {
        $pathTersimpan = [];

        try {
            $pengajuan = DB::transaction(function () use (
                $mahasiswa,
                $pengunggah,
                $hasilId,
                $alasan,
                $berkasUnggahan,
                &$pathTersimpan
            ) {
                $waktuDiajukan = now();
                $slaHariKerja = $this->slaService->ambilHariKerjaAktif('keberatan');
                $tenggatWaktu = $slaHariKerja !== null
                    ? $this->slaService->hitungTenggatWaktu($waktuDiajukan, $slaHariKerja)->toDateString()
                    : null;

                $pengajuan = PengajuanKeberatan::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'hasil_id' => $hasilId,
                    'alasan' => trim($alasan),
                    'status' => self::STATUS_DIAJUKAN,
                    'catatan_petugas' => null,
                    'diajukan_pada' => $waktuDiajukan,
                    'sla_hari_kerja' => $slaHariKerja,
                    'tenggat_waktu' => $tenggatWaktu,
                ]);

                $daftarNamaBerkas = $this->simpanBerkasKeberatan(
                    pengajuan: $pengajuan,
                    pengunggah: $pengunggah,
                    berkasUnggahan: $berkasUnggahan,
                    pathTersimpan: $pathTersimpan
                );

                $this->auditService->catat(
                    pelaku: $pengunggah,
                    aksi: 'ajukan_keberatan',
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                    nilaiSebelum: null,
                    nilaiSesudah: [
                        'mahasiswa_id' => $mahasiswa->id,
                        'hasil_id' => $hasilId,
                        'status' => self::STATUS_DIAJUKAN,
                        'sla_hari_kerja' => $slaHariKerja,
                        'tenggat_waktu' => $tenggatWaktu,
                        'jumlah_dokumen' => count($daftarNamaBerkas),
                        'dokumen' => $daftarNamaBerkas,
                    ]
                );

                return $pengajuan;
            });
        } catch (Throwable $e) {
            foreach ($pathTersimpan as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }

        // Kirim tanda terima kepada mahasiswa (sistem + antrean email KF-15)
        $this->notificationService->kirimPemberitahuanStatus(
            penerima: $pengunggah,
            judulPeristiwa: 'Tanda Terima Pengajuan Keberatan UKT #' . $pengajuan->id,
            statusBaru: self::STATUS_DIAJUKAN,
            tautanHalaman: url('/keberatan/' . $pengajuan->id),
            pemicu: $pengunggah,
            subjekTipe: PengajuanKeberatan::class,
            subjekId: $pengajuan->id,
            kirimEmail: true,
        );

        // Beri tahu daftar kerja petugas di dalam sistem
        $daftarPetugas = User::where('peran', 'petugas')->get();
        foreach ($daftarPetugas as $petugas) {
            $this->notificationService->kirimPemberitahuanStatus(
                penerima: $petugas,
                judulPeristiwa: 'Pengajuan Keberatan UKT Baru #' . $pengajuan->id . ' (' . $mahasiswa->nim . ')',
                statusBaru: self::STATUS_DIAJUKAN,
                tautanHalaman: url('/petugas/keberatan/' . $pengajuan->id),
                pemicu: $pengunggah,
                subjekTipe: PengajuanKeberatan::class,
                subjekId: $pengajuan->id,
                kirimEmail: false,
            );
        }

        return $pengajuan->load(['dokumen', 'hasil.golongan']);
    }

    /**
     * Mahasiswa melengkapi penjelasan dan/atau dokumen tambahan ketika status "Perlu Perbaikan" (Blueprint §7.3).
     *
     * @param  array<int, UploadedFile>  $berkasUnggahan
     */
    public function lengkapiPerbaikan(
        PengajuanKeberatan $pengajuan,
        User $mahasiswaUser,
        string $catatanPerbaikan,
        array $berkasUnggahan = []
    ): PengajuanKeberatan {
        if ($pengajuan->status !== self::STATUS_PERLU_PERBAIKAN) {
            throw new InvalidArgumentException('Pengajuan keberatan ini tidak sedang berada pada status Perlu Perbaikan.');
        }

        $pathTersimpan = [];

        try {
            $pengajuan = DB::transaction(function () use (
                $pengajuan,
                $mahasiswaUser,
                $catatanPerbaikan,
                $berkasUnggahan,
                &$pathTersimpan
            ) {
                $statusLama = $pengajuan->status;
                $alasanLama = $pengajuan->alasan;
                $waktuPerbaikan = now()->locale('id')->translatedFormat('d M Y H:i');
                $alasanDiperbarui = rtrim($alasanLama)
                    . "\n\n[Kelengkapan Perbaikan Mahasiswa — {$waktuPerbaikan}]\n"
                    . trim($catatanPerbaikan);

                $updated = PengajuanKeberatan::whereKey($pengajuan->id)->update([
                    'alasan' => $alasanDiperbarui,
                    'status' => self::STATUS_DALAM_PENINJAUAN,
                ]);

                if ($updated === 0) {
                    throw new InvalidArgumentException('Pengajuan keberatan tidak ditemukan untuk diperbarui.');
                }

                $daftarNamaBerkas = $this->simpanBerkasKeberatan(
                    pengajuan: $pengajuan,
                    pengunggah: $mahasiswaUser,
                    berkasUnggahan: $berkasUnggahan,
                    pathTersimpan: $pathTersimpan
                );

                $pengajuan->refresh();

                $this->auditService->catat(
                    pelaku: $mahasiswaUser,
                    aksi: 'lengkapi_perbaikan_keberatan',
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                    nilaiSebelum: [
                        'status' => $statusLama,
                    ],
                    nilaiSesudah: [
                        'status' => self::STATUS_DALAM_PENINJAUAN,
                        'catatan_perbaikan' => trim($catatanPerbaikan),
                        'dokumen_tambahan' => $daftarNamaBerkas,
                    ]
                );

                return $pengajuan;
            });
        } catch (Throwable $e) {
            foreach ($pathTersimpan as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }

        $this->notificationService->kirimPemberitahuanStatus(
            penerima: $mahasiswaUser,
            judulPeristiwa: 'Perbaikan Pengajuan Keberatan #' . $pengajuan->id . ' Diterima',
            statusBaru: self::STATUS_DALAM_PENINJAUAN,
            tautanHalaman: url('/keberatan/' . $pengajuan->id),
            pemicu: $mahasiswaUser,
            subjekTipe: PengajuanKeberatan::class,
            subjekId: $pengajuan->id,
            kirimEmail: true,
        );

        $daftarPetugas = User::where('peran', 'petugas')->get();
        foreach ($daftarPetugas as $petugas) {
            $this->notificationService->kirimPemberitahuanStatus(
                penerima: $petugas,
                judulPeristiwa: 'Kelengkapan Perbaikan Keberatan #' . $pengajuan->id . ' Masuk',
                statusBaru: self::STATUS_DALAM_PENINJAUAN,
                tautanHalaman: url('/petugas/keberatan/' . $pengajuan->id),
                pemicu: $mahasiswaUser,
                subjekTipe: PengajuanKeberatan::class,
                subjekId: $pengajuan->id,
                kirimEmail: false,
            );
        }

        return $pengajuan->load(['dokumen', 'hasil.golongan']);
    }

    /**
     * Ubah status pengajuan keberatan dengan pencatatan audit dan pemicu notifikasi KF-15.
     * Status Disetujui dan Ditolak bersifat terminal (Blueprint §7.3 & §7.6).
     */
    public function ubahStatus(
        PengajuanKeberatan $pengajuan,
        User $petugas,
        string $statusBaru,
        ?string $catatanPetugas = null
    ): PengajuanKeberatan {
        $statusDiizinkan = self::daftarStatus();

        if (! in_array($statusBaru, $statusDiizinkan, true)) {
            throw new InvalidArgumentException("Status keberatan tidak valid: {$statusBaru}");
        }

        if (self::apakahStatusTerminal($pengajuan->status)) {
            throw new InvalidArgumentException(
                "Status pengajuan keberatan yang sudah \"{$pengajuan->status}\" bersifat final (terminal) dan tidak dapat diubah lagi melalui aksi biasa."
            );
        }

        return DB::transaction(function () use ($pengajuan, $petugas, $statusBaru, $catatanPetugas) {
            $statusLama = $pengajuan->status;
            $catatanLama = $pengajuan->catatan_petugas;

            $dataUpdate = [
                'status' => $statusBaru,
                'catatan_petugas' => $catatanPetugas,
            ];

            if (self::apakahStatusTerminal($statusBaru)) {
                $dataUpdate['diputuskan_pada'] = now();
                $dataUpdate['diputuskan_oleh'] = $petugas->id;
            }

            $updated = PengajuanKeberatan::whereKey($pengajuan->id)->update($dataUpdate);

            if ($updated === 0) {
                throw new InvalidArgumentException('Pengajuan keberatan tidak ditemukan untuk diperbarui.');
            }

            $pengajuan->refresh();

            $this->auditService->catat(
                pelaku: $petugas,
                aksi: 'ubah_status_keberatan',
                subjekTipe: PengajuanKeberatan::class,
                subjekId: $pengajuan->id,
                nilaiSebelum: [
                    'status' => $statusLama,
                    'catatan_petugas' => $catatanLama,
                ],
                nilaiSesudah: [
                    'status' => $statusBaru,
                    'catatan_petugas' => $catatanPetugas,
                ]
            );

            $mahasiswaUser = $pengajuan->mahasiswa?->user;
            if ($mahasiswaUser) {
                $this->notificationService->kirimPemberitahuanStatus(
                    penerima: $mahasiswaUser,
                    judulPeristiwa: 'Perubahan Status Pengajuan Keberatan UKT',
                    statusBaru: $statusBaru,
                    tautanHalaman: url('/keberatan/'.$pengajuan->id),
                    pemicu: $petugas,
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                );
            }

            return $pengajuan;
        });
    }

    /**
     * Simpan berkas ke disk privat `local` dengan nama acak UUID dan catat nama asli di tabel dokumen_keberatan.
     *
     * @param  array<int, UploadedFile>  $berkasUnggahan
     * @param  list<string>  $pathTersimpan
     * @return list<string>
     */
    private function simpanBerkasKeberatan(
        PengajuanKeberatan $pengajuan,
        User $pengunggah,
        array $berkasUnggahan,
        array &$pathTersimpan
    ): array {
        $daftarNamaBerkas = [];

        foreach ($berkasUnggahan as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
            if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                $ext = 'pdf';
            }

            $namaAcak = Str::uuid()->toString() . '.' . $ext;
            $storedPath = Storage::disk('local')->putFileAs('dokumen-keberatan', $file, $namaAcak);

            if (! is_string($storedPath) || $storedPath === '') {
                throw new InvalidArgumentException('Gagal menyimpan berkas bukti pendukung ke penyimpanan privat.');
            }

            $pathTersimpan[] = $storedPath;
            $namaAsli = basename($file->getClientOriginalName());

            DokumenKeberatan::create([
                'pengajuan_id' => $pengajuan->id,
                'path_penyimpanan' => $storedPath,
                'nama_asli' => $namaAsli,
                'mime' => $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream',
                'ukuran' => (int) $file->getSize(),
                'diunggah_oleh' => $pengunggah->id,
            ]);

            $daftarNamaBerkas[] = $namaAsli;
        }

        return $daftarNamaBerkas;
    }
}


