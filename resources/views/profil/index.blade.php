<x-layouts.app
    title="Profil dan Pengaturan Akun"
    subtitle="Informasi identitas akun dan pembaruan kata sandi"
>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Kartu Informasi Akun -->
        <section class="glass-card p-6 md:p-8 space-y-4">
            <h2 class="text-[21px] font-heading font-bold text-ink">
                Data Identitas Akun
            </h2>

            <dl class="space-y-3 text-[14px]">
                <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex justify-between gap-4">
                    <dt class="text-ink-secondary">Nama Lengkap</dt>
                    <dd class="font-heading font-bold text-ink">{{ $user->nama }}</dd>
                </div>
                <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex justify-between gap-4">
                    <dt class="text-ink-secondary">Alamat Email</dt>
                    <dd class="font-medium text-ink">{{ $user->email }}</dd>
                </div>
                <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex justify-between gap-4">
                    <dt class="text-ink-secondary">Peran Akses</dt>
                    <dd class="font-heading font-bold text-sekunder">{{ $user->labelPeran() }}</dd>
                </div>
                @if($user->mahasiswa)
                    <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex justify-between gap-4">
                        <dt class="text-ink-secondary">NIM</dt>
                        <dd class="font-heading font-bold text-ink">{{ $user->mahasiswa->nim }}</dd>
                    </div>
                    <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex justify-between gap-4">
                        <dt class="text-ink-secondary">Program Studi / Angkatan</dt>
                        <dd class="font-medium text-ink">{{ $user->mahasiswa->program_studi }} ({{ $user->mahasiswa->angkatan }})</dd>
                    </div>
                @endif
            </dl>
        </section>

        <!-- Form Ubah Kata Sandi -->
        <section class="glass-card glass-card-glow p-6 md:p-8 space-y-4">
            <h2 class="text-[21px] font-heading font-bold text-ink">
                Perbarui Kata Sandi
            </h2>
            <p class="text-[13px] text-ink-secondary">
                Perubahan kata sandi memerlukan konfirmasi kata sandi Anda saat ini dan disimpan dengan algoritma hash aman.
            </p>

            <form method="POST" action="{{ route('profil.password') }}" class="space-y-4" data-prevent-double-submit>
                @csrf
                @method('PUT')

                <div>
                    <label for="password_lama" class="block text-[14px] font-medium text-ink mb-1.5">
                        Kata Sandi Saat Ini
                    </label>
                    <input
                        id="password_lama"
                        name="password_lama"
                        type="password"
                        required
                        class="glass-input w-full px-4 py-3 text-[14px]"
                    >
                    @error('password_lama')
                        <p class="mt-1.5 text-[13px] text-error font-medium" role="alert">! {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_baru" class="block text-[14px] font-medium text-ink mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input
                        id="password_baru"
                        name="password_baru"
                        type="password"
                        required
                        class="glass-input w-full px-4 py-3 text-[14px]"
                    >
                    @error('password_baru')
                        <p class="mt-1.5 text-[13px] text-error font-medium" role="alert">! {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_baru_confirmation" class="block text-[14px] font-medium text-ink mb-1.5">
                        Ulangi Kata Sandi Baru
                    </label>
                    <input
                        id="password_baru_confirmation"
                        name="password_baru_confirmation"
                        type="password"
                        required
                        class="glass-input w-full px-4 py-3 text-[14px]"
                    >
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary">
                        Simpan Kata Sandi Baru
                    </x-button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
