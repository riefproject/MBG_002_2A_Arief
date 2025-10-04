# Sistem Manajemen Bahan Baku untuk Program Makan Bergizi Gratis (SIMA Bahan Baku MBG)
> Repositori ini dibuat untuk memenuhi penilaian Evaluasi Tengah Semester Ganjil, D-3 Teknik Informatika, Politeknik Negeri Bandung.

## Sekilas Proyek
Sistem Manajemen Bahan Baku untuk Program Makan Bergizi Gratis (SIMA Bahan Baku MBG) ini bantu tim gudang dan dapur mengelola stok bahan baku plus permintaan menu harian. Targetnya sederhana: dapur bisa ajukan permintaan, gudang bisa review dan update stok tanpa ribet.

## Fitur Utama
- **Autentikasi dasar**: login/logout dua role (`gudang` & `dapur`).
- **Dashboard per role**: ringkasan stok dan permintaan relevan begitu login.
- **Kelola stok bahan baku**: CRUD bahan, status otomatis (tersedia, segera kadaluarsa, kadaluarsa, habis).
- **Permintaan bahan dapur**: form permintaan dengan ringkasan bahan, validasi tanggal, dan status real-time.
- **Approval gudang**: proses permintaan (setujui/tolak) sambil mengurangi stok otomatis.
- **Auto refresh status permintaan**: permintaan yang lewat tanggal masak langsung jadi `kadaluarsa` tiap user login.
- **Notifikasi toast**: feedback sukses/gagal pakai toast auto-dismiss, konsisten di semua halaman.

## Teknologi
| Komponen | Versi / Teknologi | Keterangan |
| --- | --- | --- |
| Laravel | 12.31.1 | Kerangka utama aplikasi backend |
| PHP | 8.4.13 | Runtime yang dipakai Laravel |
| Composer | 2.8.11 | Manajer dependensi PHP |
| Blade | — | Engine templating untuk halaman web |
| Tailwind CSS (via Vite) | — | Styling utility-first, dibuild bersama aset front-end |
| Alpine.js & JS Native | — | Interaksi DOM ringan: modal, tabel dinamis, notifikasi |
| PostgreSQL | 17.6 | Basis data utama |
| Cache / Queue / Session | Driver database | Menyimpan cache, antrean job, dan sesi di tabel bawaan Laravel |
| Tabel Bawaan Laravel | — | `cache`, `cache_locks`, `failed_jobs`, `job_batches`, `jobs`, `migrations`, `password_reset_tokens`, `sessions` |


## Cara Jalanin Lokal
1. Clone repo ini
   ```bash
   git clone https://github.com/riefproject/MBG_002_2A_Arief
   cd MBG_002_2A_Arief
   ```
2. Pasang dependensi PHP & JS
   ```bash
   composer install
   npm install
   ```
3. Copy env & generate key
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Sesuaikan koneksi database di `.env`, lalu migrasi + seed
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server & Vite
   ```bash
   # jalankan ini di 2 terminal berbeda

   php artisan serve # terminal 1
   npm run dev       # terminal lainnya
   ```
6. Login pakai kredensial demo (lihat [`database/seeders/UserSeeder.php`](database/seeders/UserSeeder.php)) atau pada halaman login. Role `gudang` untuk verifikasi stok, `dapur` buat pengajuan.


## Modifikasi Database

| Nama Tabel | Nama Kolom / Key | Tipe Key | Justifikasi |
|:---|:---|:---|:---|
| `users` | `remember_token` | Atribut Biasa | Dibutuhkan oleh Laravel untuk fitur autentikasi "Remember Me". |
| `users` | `users_email_unique` | Unique | Menambahkan *constraint* di database untuk memastikan email unik, sesuai deskripsi spesifikasi. |
| `permintaan` | `permintaan_pemohon_id_foreign` | Foreign Key (FK) | Mendefinisikan perilaku `ON DELETE CASCADE`; jika data `user` dihapus, semua `permintaan` terkait akan ikut terhapus. |
| `permintaan_detail` | `permintaan_detail_permintaan_id_foreign`| Foreign Key (FK) | Mendefinisikan perilaku `ON DELETE CASCADE`; jika data `permintaan` induk dihapus, semua item detailnya akan ikut terhapus. |
| `permintaan_detail` | `permintaan_detail_bahan_id_foreign` | Foreign Key (FK) | Mendefinisikan perilaku `ON DELETE SET NULL` untuk menjaga riwayat permintaan meskipun data master `bahan_baku` dihapus. |

## Lisensi
Distribusi mengikuti ketentuan [MIT License](LICENSE).
