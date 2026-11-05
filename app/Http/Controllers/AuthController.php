<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
                ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        return redirect()->intended($this->ruteBerandaPeran($user))
            ->with('toast_success', 'Selamat datang kembali, '.$user->nama.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('publik.beranda')
            ->with('toast_success', 'Anda telah keluar dari sesi sistem.');
    }

    public function profil(Request $request): View
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');

        return view('profil.index', [
            'user' => $user,
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('password_baru')),
        ]);

        // Catat perubahan kata sandi tanpa pernah menyimpan kata sandi di audit log
        $this->auditService->catat(
            pelaku: $user,
            aksi: 'ubah_kata_sandi',
            subjekTipe: User::class,
            subjekId: $user->id,
            nilaiSebelum: ['aksi' => 'pembaruan_kredensial'],
            nilaiSesudah: ['status' => 'berhasil_diperbarui']
        );

        return back()->with('toast_success', 'Kata sandi akun Anda berhasil diperbarui.');
    }

    private function ruteBerandaPeran(User $user): string
    {
        return match ($user->peran) {
            User::PERAN_PETUGAS => route('petugas.beranda'),
            User::PERAN_PIMPINAN => route('pimpinan.rekap'),
            default => route('mahasiswa.beranda'),
        };
    }
}

