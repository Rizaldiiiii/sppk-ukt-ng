<x-layouts.guest title="Masuk ke Akun SPPK-UKT UNG">
    <div class="max-w-lg mx-auto py-4">
        <div class="neu-card p-6 md:p-8 overflow-hidden relative">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            <!-- Brand Logo UNG 56–64px di atas form (Neumorphism.md §5.10) -->
            <div class="text-center mb-6 pt-2">
                <div class="inline-flex items-center justify-center p-3 rounded-[12px] bg-[#f8f8f7] shadow-[5px_5px_10px_#D9D3CD,-5px_-5px_10px_#FFFFFF] mb-4">
                    <img
                        src="{{ asset('images/logo-ung.png') }}"
                        alt="Universitas Negeri Gorontalo"
                        class="w-16 h-16 object-contain"
                    >
                </div>
                <div class="font-heading font-bold text-[15px] text-[#550b14]">
                    Universitas Negeri Gorontalo
                </div>
                <h1 class="text-[26px] font-heading font-extrabold text-[#1C1917] mt-1">
                    Masuk ke SPPK-UKT UNG
                </h1>
                <p class="text-[14px] text-[#57534E] mt-1">
                    Gunakan akun terdaftar untuk melihat rincian hasil penilaian UKT pribadi atau mengelola layanan.
                </p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" data-prevent-double-submit novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-[14px] font-medium text-[#1C1917] mb-2">
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
                        class="neu-input w-full px-4 py-3 text-[14px] @error('email') border-[#991B1B] @enderror"
                    >
                    @error('email')
                        <p id="email-error" class="mt-2 text-[13px] text-[#991B1B] flex items-center gap-1.5 font-medium" role="alert">
                            <span aria-hidden="true" class="font-bold">!</span>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-[14px] font-medium text-[#1C1917] mb-2">
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
                        class="neu-input w-full px-4 py-3 text-[14px] @error('password') border-[#991B1B] @enderror"
                    >
                    @error('password')
                        <p id="password-error" class="mt-2 text-[13px] text-[#991B1B] flex items-center gap-1.5 font-medium" role="alert">
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

            <div class="mt-6 pt-5 border-t border-[#DDD9D6] text-center">
                <p class="text-[13px] text-[#57534E]">
                    Ingin melihat alur penentuan, faktor penilaian, atau rentang nominal tanpa login?
                    <a href="{{ route('publik.beranda') }}" class="font-semibold text-[#550b14] underline hover:text-[#470912]">
                        Buka Halaman Informasi Publik
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>

