# Akun Contoh — SPPK-UKT UNG, Tahap 1

Semua akun di bawah ini **data fiktif / dummy untuk seeder dan demo lokal saja**, bukan akun mahasiswa/petugas asli dan bukan penetapan resmi kampus. Kata sandi di sini sengaja dibuat mudah diingat untuk demo — ganti sebelum dipakai di luar lokal.

Seeder wajib membuat akun-akun ini persis seperti tabel di bawah, supaya Antigravity dan kamu menguji dengan akun yang sama.

| Peran | Nama | Email | Kata sandi | NIM / Keterangan |
|---|---|---|---|---|
| Petugas UKT | Dewi Petugas | petugas@ung.ac.id | `Petugas123!` | Bisa entri hasil resmi, kelola parameter, kelola konten publik |
| Mahasiswa — hasil resmi | Budi Pratama | budi.pratama@student.ung.ac.id | `Mahasiswa123!` | NIM `531412001`, Prodi Informatika, Golongan IV, Rp 2.500.000 / semester, hasil **resmi** + tabel rincian lengkap |
| Mahasiswa — hasil simulasi | Ayu Lestari | ayu.lestari@student.ung.ac.id | `Mahasiswa123!` | NIM `531412002`, hasil **simulasi** (footer seeder/simulasi wajib tampil di `/hasil-saya`) |
| Mahasiswa — tanpa hasil | Citra Kirana | citra.kirana@student.ung.ac.id | `Mahasiswa123!` | NIM `531412003`, belum ada hasil — tampilkan empty state, bukan tabel kosong |
| Pimpinan / auditor | Pimpinan UNG | pimpinan@ung.ac.id | `Pimpinan123!` | Hanya rekap agregat, tanpa daftar mahasiswa dan tanpa data ekonomi perorangan |

## Catatan pengujian

- Login sebagai Budi, lalu coba buka data Ayu lewat URL langsung → harus ditolak (403), bukan dialihkan diam-diam.
- Login sebagai Ayu → `/hasil-saya` harus menampilkan footer penjelasan simulasi/seeder, bukan badge `SIMULASI` di tiap baris.
- Login sebagai Citra → `/hasil-saya` harus menampilkan empty state yang menjelaskan langkah selanjutnya.
- Login sebagai pimpinan → hanya rekap agregat, tidak ada nama/NIM/daftar mahasiswa.
- Semua kata sandi di atas di-hash di database (bcrypt). Jangan menulis kata sandi polos di log atau email.

## Perintah jalan lokal

```bash
# 1. Pastikan MySQL di XAMPP sudah Start, lalu buat database dulu:
mysql -u root -e "CREATE DATABASE sppk_ukt_ung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# (atau buat lewat phpMyAdmin dengan nama sppk_ukt_ung)

# 2. Salin .env dan generate key:
cp .env.contoh-tahap1 .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan queue:work
php artisan serve
```

Lalu buka http://127.0.0.1:8000 dan login dengan salah satu akun di atas.
