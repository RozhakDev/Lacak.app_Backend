# Lacak.app

## Tentang

**Lacak.app Backend** adalah layanan REST API dan panel manajemen Bursa Kerja Khusus (BKK) untuk Sekolah Menengah Kejuruan (SMK). Layanan ini mengelola data penelusuran lulusan (*Tracer Study*), lowongan pekerjaan, agenda kegiatan karier, dan profil alumni dalam satu sistem terintegrasi.

Sistem menerapkan arsitektur *multi-tenant* (satu basis data dengan isolasi lingkup sekolah) yang memungkinkan admin BKK sekolah mengelola data institusinya secara mandiri, sementara Super Admin dapat memantau dan merekapitulasi data agregat di tingkat pusat. Layanan backend dibangun menggunakan Laravel 11 dengan autentikasi berbasis token Laravel Sanctum untuk aplikasi klien (Mobile/Web), serta panel admin berbasis MoonShine v4 dengan kontrol akses berbasis peran (*Role-Based Access Control*).

## Persyaratan

- **PHP:** `>= 8.3` dengan ekstensi `pdo_mysql` / `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd` / `imagick`, `xml`, `bcmath`, dan `tokenizer`.
- **Composer:** `>= 2.2`
- **Basis Data:** SQLite 3 (pengembangan lokal) atau MySQL 8.0+ / PostgreSQL 15+ (server/produksi).
- **Web Server:** Nginx atau Apache dengan konfigurasi *URL rewriting* aktif.

## Instalasi

1. Kloning repositori:
   
   ```bash
   git clone https://github.com/RozhakDev/Lacak.app_Backend.git
   cd Lacak.app_Backend
   ```

2. Pasang dependensi pustaka:
   
   ```bash
   composer install
   ```

3. Siapkan berkas konfigurasi lingkungan dan buat kunci aplikasi:
   
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Jalankan migrasi skema dan data awal (*seeders*):
   
   ```bash
   # Pengembangan lokal (SQLite)
   touch database/database.sqlite
   php artisan migrate --seed
   
   # Server staging / produksi (MySQL / PostgreSQL)
   php artisan migrate --seed
   ```

5. Buat tautan penyimpanan publik dan kompilasi spesifikasi OpenAPI:
   
   ```bash
   php artisan storage:link
   php artisan l5-swagger:generate
   ```

6. Jalankan server:
   
   ```bash
   php artisan serve
   ```

## Evaluasi

| Modul / Domain Pengujian            | Cakupan Pengujian                                                              | Jumlah Test | Jumlah Asersi | Status         |
|:----------------------------------- |:------------------------------------------------------------------------------ |:-----------:|:-------------:|:--------------:|
| **Autentikasi & Akun**              | Registrasi NISN, Login, Verifikasi OTP, Password Reset, Rate Limiting          | 20          | 56            | Lulus          |
| **Isolasi Multi-Tenant & RBAC**     | Tenant Scoping (`SchoolScope`), Policy Gate Admin/Super Admin, IDOR Protection | 26          | 72            | Lulus          |
| **Tracer Study Engine**             | Conditional Submission (Kerja/Kuliah/Wirausaha), DB Transaction Rollback       | 9           | 32            | Lulus          |
| **Bursa Kerja (Job Vacancy)**       | Browsing Loker, Validasi Unggah CV (MIME & Ukuran), Pelacakan Lamaran          | 18          | 48            | Lulus          |
| **Kegiatan (Event & Workshop)**     | Registrasi Peserta, Event Browsing, Pencegahan Duplikasi Partisipasi           | 21          | 54            | Lulus          |
| **Profil & Pengalaman Alumni**      | CRUD Pengalaman Kerja/Organisasi, Cross-Field Date Validation                  | 8           | 26            | Lulus          |
| **Master Data & Integritas Sistem** | Katalog Jurusan per Sekolah, Tracer Options, Empirical Boundary Verification   | 13          | 32            | Lulus          |
| **Total**                           | **Rangkaian Lengkap (Automated Regression Suite)**                             | **115**     | **320**       | **100% LULUS** |

## Catatan

> Sebelum melakukan penerapan di lingkungan produksi, sesuaikan variabel `.env` (`APP_ENV=production` dan `APP_DEBUG=false`), perbarui seluruh kredensial akun bawaan seeder, pastikan direktori `storage/` dan `bootstrap/cache/` memiliki izin tulis untuk *user* web server, serta selaraskan konfigurasi *trusted proxy* dan SSL jika layanan berjalan di balik Nginx atau Cloudflare.

## Lisensi

Didistribusikan di bawah lisensi open-source **[MIT License](LICENSE)**.
