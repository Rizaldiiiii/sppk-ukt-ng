<x-layouts.app
    title="Profil dan Pengaturan Akun"
    subtitle="Informasi identitas akun dan pembaruan kata sandi"
>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Kartu Informasi Akun -->
        <section class="neu-card p-6 md:p-8 space-y-4">
            <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
                Data Identitas Akun
            </h2>

            <dl class="space-y-3 text-[14px]">
                <div class="p-3.5 rounded-[10px] bg-white border border-[#E2DFDC] flex justify-between gap-4">
                    <dt class="text-[#57534E]">Nama Lengkap</dt>
                    <dd class="font-heading font-bold text-[#1C1917]">{{ $user->nama }}</dd>
                </div>
                <div class="p-3.5 rounded-[10px] bg-white border border-[#E2DFDC] flex justify-between gap-4">
                    <dt class="text-[#57534E]">Alamat Email</dt>
                    <dd class="font-medium text-[#1C1917]">{{ $user->email }}</dd>
                </div>
                <div class="p-3.5 rounded-[10px] bg-white border border-[#E2DFDC] flex justify-between gap-4">
                    <dt class="text-[#57534E]">Peran Akses</dt>
                    <dd class="font-heading font-bold text-[#550b14]">{{ $user->labelPeran() }}</dd>
                </div>
                @if($user->mahasiswa)
                    <div class="p-3.5 rounded-[10px] bg-white border border-[#E2DFDC] flex justify-between gap-4">
                        <dt class="text-[#57534E]">NIM</dt>
                        <dd class="font-heading font-bold text-[#1C1917]">{{ $user->mahasiswa->nim }}</dd>
                    </div>
                    <div class="p-3.5 rounded-[10px] bg-white border border-[#E2DFDC] flex justify-between gap-4">
                        <dt class="text-[#57534E]">Program Studi / Angkatan</dt>
                        <dd class="font-medium text-[#1C1917]">{{ $user->mahasiswa->program_studi }} ({{ $user->mahasiswa->angkatan }})</dd>
                    </div>
                @endif
            </dl>
        </section>

        <!-- Form Ubah Kata Sandi -->
        <section class="neu-card p-6 md:p-8 space-y-4">
            <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
                Perbarui Kata Sandi
            </h2>
            <p class="text-[13px] text-[#57534E]">
                Perubahan kata sandi memerlukan konfirmasi kata sandi Anda saat ini dan disimpan dengan algoritma hash aman.
            </p>

            <form method="POST" action="{{ route('profil.password') }}" class="space-y-4" data-prevent-double-submit>
                @csrf
                @method('PUT')

                <div>
                    <label for="password_lama" class="block text-[14px] font-medium text-[#1C1917] mb-1.5">
                        Kata Sandi Saat Ini
                    </label>
                    <input
                        id="password_lama"
                        name="password_lama"
                        type="password"
                        required
                        class="neu-input w-full px-4 py-3 text-[14px]"
                    >
                    @error('password_lama')
                        <p class="mt-1.5 text-[13px] text-[#991B1B] font-medium" role="alert">! {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_baru" class="block text-[14px] font-medium text-[#1C1917] mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input
                        id="password_baru"
                        name="password_baru"
                        type="password"
                        required
                        class="neu-input w-full px-4 py-3 text-[14px]"
                    >
                    @error('password_baru')
                        <p class="mt-1.5 text-[13px] text-[#991B1B] font-medium" role="alert">! {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_baru_confirmation" class="block text-[14px] font-medium text-[#1C1917] mb-1.5">
                        Ulangi Kata Sandi Baru
                    </label>
                    <input
                        id="password_baru_confirmation"
                        name="password_baru_confirmation"
                        type="password"
                        required
                        class="neu-input w-full px-4 py-3 text-[14px]"
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

