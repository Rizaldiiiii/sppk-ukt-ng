<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\KontenPublik;
use App\Models\PengajuanKeberatan;
use App\Services\ObjectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class KeberatanController extends Controller
{
    public function __construct(
        private readonly ObjectionService $objectionService,
    ) {}

    /**
     * Daftar pengajuan keberatan milik mahasiswa yang sedang login (/keberatan) — KF-07.
     */
    public function index(Request $request): View
    {
        Gate::authorize('create', PengajuanKeberatan::class);

        $mahasiswa = $request->user()->mahasiswa;

        $daftarKeberatan = PengajuanKeberatan::with(['hasil.golongan', 'dokumen', 'pemutus'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate(10);

        return view('mahasiswa.keberatan.index', [
            'mahasiswa' => $mahasiswa,
            'daftarKeberatan' => $daftarKeberatan,
        ]);
    }

    /**
     * Formulir pengajuan keberatan baru beserta petunjuk pengisian per kolom (/keberatan/baru) — KF-07 & KF-09.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', PengajuanKeberatan::class);

        $mahasiswa = $request->user()->mahasiswa->load(['hasilUkt.golongan', 'hasilTerbaru.golongan']);
        $petunjuk = KontenPublik::petunjukPengisian();

        $formToken = (string) Str::uuid();
        $request->session()->put('keberatan_form_token', $formToken);

        return view('mahasiswa.keberatan.create', [
            'mahasiswa' => $mahasiswa,
            'daftarHasil' => $mahasiswa->hasilUkt,
            'hasilTerbaru' => $mahasiswa->hasilTerbaru,
            'petunjuk' => $petunjuk,
            'formToken' => $formToken,
            'maxFiles' => ObjectionService::MAX_FILES,
            'maxFileSizeKb' => ObjectionService::MAX_FILE_SIZE_KB,
        ]);
    }

    /**
     * Simpan pengajuan keberatan baru (/keberatan) — KF-07 & KF-09.
     * Menolak kiriman tanpa alasan, menjaga draf isian saat validasi gagal, dan mencegah kirim ganda.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', PengajuanKeberatan::class);

        $mahasiswa = $request->user()->mahasiswa;

        $validated = $request->validate(
            [
                'hasil_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('hasil_ukt', 'id')->where('mahasiswa_id', $mahasiswa->id),
                ],
                'alasan' => ['required', 'string', 'min:20', 'max:3000'],
                'dokumen' => ['nullable', 'array', 'max:' . ObjectionService::MAX_FILES],
                'dokumen.*' => [
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'mimetypes:application/pdf,image/jpeg,image/png',
                    'max:' . ObjectionService::MAX_FILE_SIZE_KB,
                ],
            ],
            [
                'hasil_id.exists' => 'Kolom Hasil UKT yang Diajukan tidak valid atau bukan milik akun Anda. Pilih salah satu hasil UKT yang tersedia pada daftar pilihan.',
                'alasan.required' => 'Kolom Alasan Keberatan wajib diisi. Jelaskan komponen penilaian yang dinilai tidak sesuai beserta kondisi ekonomi keluarga Anda.',
                'alasan.min' => 'Kolom Alasan Keberatan terlalu singkat (minimal 20 karakter). Uraikan komponen yang diajukan keberatan dan alasan perubahannya secara lengkap.',
                'alasan.max' => 'Kolom Alasan Keberatan melebihi batas maksimal 3.000 karakter. Ringkas uraian Anda pada poin-poin utama.',
                'dokumen.array' => 'Kolom Dokumen Bukti Pendukung harus berupa daftar berkas unggahan yang valid.',
                'dokumen.max' => 'Kolom Dokumen Bukti Pendukung melebihi batas sementara maksimal ' . ObjectionService::MAX_FILES . ' berkas per pengajuan. Silakan pilih berkas bukti yang paling utama.',
                'dokumen.*.file' => 'Berkas pada kolom Dokumen Bukti Pendukung gagal diunggah. Pastikan berkas tidak rusak lalu ulangi.',
                'dokumen.*.mimes' => 'Format berkas pada kolom Dokumen Bukti Pendukung tidak didukung. Unggah berkas berformat PDF, JPG, atau PNG.',
                'dokumen.*.mimetypes' => 'Tipe berkas pada kolom Dokumen Bukti Pendukung tidak sesuai ketentuan. Gunakan berkas asli berformat PDF, JPG, atau PNG.',
                'dokumen.*.max' => 'Ukuran berkas pada kolom Dokumen Bukti Pendukung melebihi batas sementara 2 MB (2.048 KB) per berkas. Perkecil atau kompres ukuran berkas terlebih dahulu.',
            ]
        );

        // Pencegahan kirim ganda / klik ganda (idempotency token & jendela duplikasi 15 detik)
        $submittedToken = $request->input('form_token');
        $sessionToken = $request->session()->pull('keberatan_form_token');

        $duplikatBaruSaja = PengajuanKeberatan::where('mahasiswa_id', $mahasiswa->id)
            ->where('alasan', trim($validated['alasan']))
            ->where('created_at', '>=', now()->subSeconds(15))
            ->latest('id')
            ->first();

        if ($duplikatBaruSaja || ($submittedToken !== null && $sessionToken !== null && ! hash_equals((string) $sessionToken, (string) $submittedToken))) {
            $target = $duplikatBaruSaja ?? PengajuanKeberatan::where('mahasiswa_id', $mahasiswa->id)->latest('id')->first();
            if ($target) {
                return redirect()
                    ->route('mahasiswa.keberatan.show', $target)
                    ->with('toast_success', 'Pengajuan keberatan #' . $target->id . ' sudah tercatat (pengiriman ganda berhasil dicegah).');
            }
        }

        $hasilId = isset($validated['hasil_id']) && $validated['hasil_id'] !== ''
            ? (int) $validated['hasil_id']
            : $mahasiswa->hasilTerbaru?->id;

        $berkasUnggahan = $request->file('dokumen', []) ?: [];

        $pengajuan = $this->objectionService->ajukan(
            mahasiswa: $mahasiswa,
            pengunggah: $request->user(),
            hasilId: $hasilId,
            alasan: $validated['alasan'],
            berkasUnggahan: is_array($berkasUnggahan) ? $berkasUnggahan : [$berkasUnggahan]
        );

        return redirect()
            ->route('mahasiswa.keberatan.show', $pengajuan)
            ->with(
                'toast_success',
                'Pengajuan keberatan #' . $pengajuan->id . ' berhasil dikirim dengan status "' . $pengajuan->status . '". Tanda terima telah dikirim ke pemberitahuan sistem.'
            );
    }

    /**
     * Detail pengajuan keberatan (/keberatan/{pengajuan}) — memeriksa kepemilikan melalui KeberatanPolicy.
     */
    public function show(Request $request, PengajuanKeberatan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        $pengajuan->load(['mahasiswa.user', 'hasil.golongan', 'dokumen', 'pemutus']);

        $riwayatStatus = AuditLog::with('pelaku')
            ->where('subjek_tipe', PengajuanKeberatan::class)
            ->where('subjek_id', (string) $pengajuan->id)
            ->orderBy('dibuat_pada')
            ->orderBy('id')
            ->get();

        $petunjuk = KontenPublik::petunjukPengisian();

        return view('mahasiswa.keberatan.show', [
            'pengajuan' => $pengajuan,
            'riwayatStatus' => $riwayatStatus,
            'petunjuk' => $petunjuk,
            'maxFiles' => ObjectionService::MAX_FILES,
            'maxFileSizeKb' => ObjectionService::MAX_FILE_SIZE_KB,
        ]);
    }

    /**
     * Mahasiswa melengkapi penjelasan dan/atau dokumen tambahan ketika status "Perlu Perbaikan" (KF-07 & KF-09).
     */
    public function perbaikan(Request $request, PengajuanKeberatan $pengajuan): RedirectResponse
    {
        Gate::authorize('view', $pengajuan);

        if ($pengajuan->status !== ObjectionService::STATUS_PERLU_PERBAIKAN) {
            return back()->with(
                'toast_error',
                'Pengajuan keberatan ini sedang berstatus "' . $pengajuan->status . '" dan tidak memerlukan pengiriman perbaikan.'
            );
        }

        $validated = $request->validate(
            [
                'catatan_perbaikan' => ['required', 'string', 'min:15', 'max:2000'],
                'dokumen' => ['nullable', 'array', 'max:' . ObjectionService::MAX_FILES],
                'dokumen.*' => [
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'mimetypes:application/pdf,image/jpeg,image/png',
                    'max:' . ObjectionService::MAX_FILE_SIZE_KB,
                ],
            ],
            [
                'catatan_perbaikan.required' => 'Kolom Keterangan Kelengkapan Perbaikan wajib diisi. Jelaskan tindak lanjut Anda atas catatan petugas.',
                'catatan_perbaikan.min' => 'Kolom Keterangan Kelengkapan Perbaikan terlalu singkat (minimal 15 karakter). Uraikan penjelasan perbaikan atau dokumen tambahan yang dilampirkan.',
                'catatan_perbaikan.max' => 'Kolom Keterangan Kelengkapan Perbaikan melebihi batas maksimal 2.000 karakter.',
                'dokumen.max' => 'Kolom Dokumen Tambahan melebihi batas sementara maksimal ' . ObjectionService::MAX_FILES . ' berkas sekali unggah.',
                'dokumen.*.mimes' => 'Format berkas pada kolom Dokumen Tambahan tidak didukung. Unggah berkas berformat PDF, JPG, atau PNG.',
                'dokumen.*.mimetypes' => 'Tipe berkas pada kolom Dokumen Tambahan tidak sesuai ketentuan. Gunakan berkas asli berformat PDF, JPG, atau PNG.',
                'dokumen.*.max' => 'Ukuran berkas pada kolom Dokumen Tambahan melebihi batas sementara 2 MB (2.048 KB) per berkas. Kompres berkas terlebih dahulu.',
            ]
        );

        $berkasUnggahan = $request->file('dokumen', []) ?: [];

        try {
            $this->objectionService->lengkapiPerbaikan(
                pengajuan: $pengajuan,
                mahasiswaUser: $request->user(),
                catatanPerbaikan: $validated['catatan_perbaikan'],
                berkasUnggahan: is_array($berkasUnggahan) ? $berkasUnggahan : [$berkasUnggahan]
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('toast_error', $e->getMessage());
        }

        return redirect()
            ->route('mahasiswa.keberatan.show', $pengajuan)
            ->with('toast_success', 'Kelengkapan perbaikan berhasil dikirim. Status pengajuan kini kembali menjadi "Dalam Peninjauan".');
    }
}


