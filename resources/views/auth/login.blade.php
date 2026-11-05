<x-layouts.guest title="Masuk ke Akun SPPK-UKT UNG">
    <div class="max-w-lg mx-auto py-4">
        <div class="glass-card p-6 md:p-8 overflow-hidden relative">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            <!-- Brand Logo UNG 56–64px di atas form (Glassmorphism.md §5.10) -->
            <div class="text-center mb-6 pt-2">
                <div class="inline-flex items-center justify-center p-3 rounded-[12px] bg-primer/90 border border-border-glass shadow-glass-sm mb-4">
                    <img
                        src="{{ asset('images/logo-ung.png') }}"
                        alt="Universitas Negeri Gorontalo"
                        class="w-16 h-16 object-contain"
                    >
                </div>
                <div class="font-heading font-bold text-[15px] text-sekunder">
                    Universitas Negeri Gorontalo
                </div>
                <h1 class="text-[26px] font-heading font-extrabold text-ink mt-1">
                    Masuk ke SPPK-UKT UNG
                </h1>
                <p class="text-[14px] text-ink-secondary mt-1">
                    Gunakan akun terdaftar untuk melihat rincian hasil penilaian UKT pribadi atau mengelola layanan.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" data-prevent-double-submit novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-[14px] font-medium text-ink mb-2">
                        Alamat Email Institusi / Mahasiswa
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="nama@student.ung.ac.id"
                        aria-describedby="@error('email') email-error @enderror"
                        class="glass-input w-full px-4 py-3 text-[14px] @error('email') border-error @enderror"
                    >
                    @error('email')
                        <p id="email-error" class="mt-2 text-[13px] text-error flex items-center gap-1.5 font-medium" role="alert">
                            <span aria-hidden="true" class="font-bold">!</span>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-[14px] font-medium text-ink mb-2">
                        Kata Sandi
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                        aria-describedby="@error('password') password-error @enderror"
                        class="glass-input w-full px-4 py-3 text-[14px] @error('password') border-error @enderror"
                    >
                    @error('password')
                        <p id="password-error" class="mt-2 text-[13px] text-error flex items-center gap-1.5 font-medium" role="alert">
                            <span aria-hidden="true" class="font-bold">!</span>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary" class="w-full">
                        Masuk ke Sistem
                    </x-button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-divider/80 text-center">
                <p class="text-[13px] text-ink-secondary">
                    Ingin melihat alur penentuan, faktor penilaian, atau rentang nominal tanpa login?
                    <a href="{{ route('publik.beranda') }}" class="font-semibold text-sekunder underline hover:text-sekunder-hover">
                        Buka Halaman Informasi Publik
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>
