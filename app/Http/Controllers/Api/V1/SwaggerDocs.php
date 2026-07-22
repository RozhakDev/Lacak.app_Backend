<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

class SwaggerDocs
{
    #[OA\Post(
        path: "/api/v1/auth/register",
        summary: "Pendaftaran Akun Alumni",
        description: "Mendaftarkan entitas akun baru khusus untuk pengguna (Alumni). Sistem akan melakukan validasi unik pada NISN dan Email, lalu men-generate OTP untuk aktivasi.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "nisn", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Ahmad Budi Santoso"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "alumni@smk-contoh.sch.id"),
                    new OA\Property(property: "nisn", type: "string", example: "0011223344"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123!"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Secret123!")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Pendaftaran berhasil, sistem mengirimkan kode OTP ke email."),
            new OA\Response(response: 422, description: "Gagal validasi struktur payload.")
        ]
    )]
    public function register() {}

    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Otentikasi Pengguna (Login)",
        description: "Melakukan verifikasi kredensial pengguna (NISN atau Email). Mengembalikan token Bearer Sanctum yang akan digunakan untuk seluruh akses terproteksi.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["identifier", "password"],
                properties: [
                    new OA\Property(property: "identifier", type: "string", description: "Bisa menggunakan Email atau NISN", example: "alumni@smk-contoh.sch.id"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123!")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Otentikasi berhasil. Token diberikan."),
            new OA\Response(response: 401, description: "Kredensial tidak valid."),
            new OA\Response(response: 403, description: "Akun belum diverifikasi OTP.")
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: "/api/v1/auth/verify-email",
        summary: "Verifikasi Kode OTP Email",
        description: "Memverifikasi token OTP 6 digit yang dikirimkan ke email alumni pada saat pendaftaran awal.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "alumni@smk-contoh.sch.id"),
                    new OA\Property(property: "otp", type: "string", example: "123456")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Email berhasil diverifikasi, status akun menjadi aktif."),
            new OA\Response(response: 400, description: "OTP salah atau kedaluwarsa.")
        ]
    )]
    public function verifyEmail() {}

    #[OA\Post(
        path: "/api/v1/auth/resend-otp",
        summary: "Kirim Ulang OTP",
        description: "Meminta sistem untuk men-generate ulang dan mengirimkan OTP baru ke email pengguna (berlaku untuk konteks verifikasi atau reset password).",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "context"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "alumni@smk-contoh.sch.id"),
                    new OA\Property(property: "context", type: "string", enum: ["verify", "reset"], example: "verify")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "OTP baru berhasil diterbitkan dan dikirim.")
        ]
    )]
    public function resendOtp() {}

    #[OA\Post(
        path: "/api/v1/auth/forgot-password",
        summary: "Lupa Kata Sandi (Permintaan OTP)",
        description: "Menginisiasi alur pemulihan akun. Sistem mengirimkan kode OTP khusus ke email pengguna yang terdaftar.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "alumni@smk-contoh.sch.id")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Instruksi pemulihan berhasil dikirim.")
        ]
    )]
    public function forgotPassword() {}

    #[OA\Post(
        path: "/api/v1/auth/reset-password",
        summary: "Pemulihan Kata Sandi (Reset)",
        description: "Mengganti kata sandi lama dengan yang baru. Memerlukan kode OTP sah dari alur Lupa Kata Sandi.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp", "new_password", "new_password_confirmation"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "alumni@smk-contoh.sch.id"),
                    new OA\Property(property: "otp", type: "string", example: "123456"),
                    new OA\Property(property: "new_password", type: "string", format: "password", example: "NewSecret123!"),
                    new OA\Property(property: "new_password_confirmation", type: "string", format: "password", example: "NewSecret123!")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Kata sandi berhasil diperbarui.")
        ]
    )]
    public function resetPassword() {}

    #[OA\Post(
        path: "/api/v1/auth/logout",
        summary: "Akhiri Sesi (Logout)",
        description: "Menghapus dan menonaktifkan token akses Bearer saat ini sehingga tidak dapat digunakan kembali.",
        security: [["sanctum" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 200, description: "Sesi berhasil diakhiri.")
        ]
    )]
    public function logout() {}

    #[OA\Get(
        path: "/api/v1/profile",
        summary: "Lihat Profil Alumni Saat Ini",
        description: "Mengambil data profil lengkap dari alumni yang sedang terotentikasi, beserta relasi portofolio dan riwayat pengalaman.",
        security: [["sanctum" => []]],
        tags: ["Alumni Profile"],
        responses: [
            new OA\Response(response: 200, description: "Payload data profil sukses diambil.")
        ]
    )]
    public function getProfile() {}

    #[OA\Post(
        path: "/api/v1/profile",
        summary: "Perbarui Data Profil Alumni",
        description: "Melakukan pembaruan terhadap atribut profil alumni. Menggunakan Content-Type multipart/form-data untuk memfasilitasi unggahan Avatar dan Dokumen Resume (CV).",
        security: [["sanctum" => []]],
        tags: ["Alumni Profile"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "major_id", type: "integer", example: 1),
                        new OA\Property(property: "graduation_year", type: "integer", example: 2024),
                        new OA\Property(property: "phone_number", type: "string", example: "08123456789"),
                        new OA\Property(property: "avatar", type: "string", format: "binary", description: "Berkas gambar (JPG/PNG/WEBP), max 2MB"),
                        new OA\Property(property: "resume", type: "string", format: "binary", description: "Berkas dokumen (PDF), max 5MB"),
                        new OA\Property(property: "about_me", type: "string", example: "Seorang software engineer..."),
                        new OA\Property(property: "skills[0]", type: "string", example: "PHP"),
                        new OA\Property(property: "skills[1]", type: "string", example: "React"),
                        new OA\Property(property: "linkedin_url", type: "string", example: "https://linkedin.com/in/alumni"),
                        new OA\Property(property: "portfolio_url", type: "string", example: "https://alumni.dev")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Data profil berhasil disinkronisasi.")
        ]
    )]
    public function updateProfile() {}

    #[OA\Post(
        path: "/api/v1/profile/experiences",
        summary: "Tambahkan Pengalaman Baru",
        description: "Mendaftarkan rekam jejak pengalaman kerja, organisasi, atau sertifikasi ke dalam profil alumni.",
        security: [["sanctum" => []]],
        tags: ["Alumni Experience"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["company_name", "position", "start_date"],
                properties: [
                    new OA\Property(property: "company_name", type: "string", example: "PT Teknologi Bangsa"),
                    new OA\Property(property: "position", type: "string", example: "Backend Developer"),
                    new OA\Property(property: "description", type: "string", example: "Mengembangkan arsitektur microservices..."),
                    new OA\Property(property: "start_date", type: "string", format: "date", example: "2023-01-15"),
                    new OA\Property(property: "end_date", type: "string", format: "date", example: "2024-01-15"),
                    new OA\Property(property: "is_current", type: "boolean", example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Pengalaman baru direkam.")
        ]
    )]
    public function addExperience() {}

    #[OA\Put(
        path: "/api/v1/profile/experiences/{id}",
        summary: "Ubah Data Pengalaman",
        description: "Melakukan mutasi data pada rekam jejak pengalaman yang sudah ada berdasarkan ID yang spesifik.",
        security: [["sanctum" => []]],
        tags: ["Alumni Experience"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "company_name", type: "string", example: "PT Teknologi Bangsa Terdepan"),
                    new OA\Property(property: "position", type: "string", example: "Senior Backend Developer"),
                    new OA\Property(property: "is_current", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Pengalaman sukses diperbarui.")
        ]
    )]
    public function updateExperience() {}

    #[OA\Delete(
        path: "/api/v1/profile/experiences/{id}",
        summary: "Hapus Pengalaman",
        description: "Menghapus rekam jejak pengalaman secara permanen dari profil alumni.",
        security: [["sanctum" => []]],
        tags: ["Alumni Experience"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Data pengalaman berhasil dihapus.")
        ]
    )]
    public function deleteExperience() {}

    #[OA\Post(
        path: "/api/v1/tracer/submissions",
        summary: "Kirim Kuesioner Tracer Study",
        description: "Merekam hasil kuesioner pelacakan alumni. Payload bersifat polimorfik dan akan dievaluasi secara dinamis berdasarkan parameter `status` (bekerja, kuliah, wirausaha).",
        security: [["sanctum" => []]],
        tags: ["Tracer Study"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["bekerja", "kuliah", "wirausaha"], example: "bekerja"),
                    new OA\Property(property: "location_scale", type: "string", example: "dalam_kota"),
                    new OA\Property(property: "location_country", type: "string", example: "dalam_negeri"),
                    new OA\Property(property: "field_of_work", type: "string", example: "Software Engineering"),
                    new OA\Property(property: "salary_range", type: "string", example: "5_-_10_juta"),
                    new OA\Property(property: "company_name", type: "string", example: "PT Maju Mundur"),
                    new OA\Property(property: "position", type: "string", example: "Junior Developer"),
                    new OA\Property(property: "start_date", type: "string", format: "date", example: "2024-02-01"),
                    new OA\Property(property: "is_linear", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Laporan Tracer Study berhasil direkam.")
        ]
    )]
    public function submitTracer() {}

    #[OA\Get(
        path: "/api/v1/tracer/export",
        summary: "Ekspor Laporan Tracer (Excel)",
        description: "Menghasilkan format berkas Excel (.xlsx) murni yang merangkum keseluruhan entri partisipasi Tracer Study untuk pelaporan akreditasi BKK.",
        security: [["sanctum" => []]],
        tags: ["Tracer Study"],
        responses: [
            new OA\Response(response: 200, description: "Stream unduhan berkas biner Excel.")
        ]
    )]
    public function exportTracer() {}

    #[OA\Get(
        path: "/api/v1/master/majors",
        summary: "Daftar Master Konsentrasi Jurusan",
        description: "Menampilkan senarai referensi statis untuk atribut Jurusan di SMK (contoh: TKJ, RPL, TKR). Diperuntukkan sebagai pengisi opsi dropdown pada Frontend.",
        tags: ["Master Data"],
        responses: [
            new OA\Response(response: 200, description: "Sukses mengembalikan matriks daftar jurusan.")
        ]
    )]
    public function getMajors() {}

    #[OA\Get(
        path: "/api/v1/master/tracer-options",
        summary: "Daftar Meta Parameter Kuesioner Tracer",
        description: "Menampilkan himpunan Enumerasi (opsi-opsi valid) untuk form pengisian Tracer Study seperti klasifikasi rentang gaji, skala lokasi perusahaan, maupun batas omset wirausaha.",
        tags: ["Master Data"],
        responses: [
            new OA\Response(response: 200, description: "Sukses mengembalikan peta struktur form kuesioner dinamis.")
        ]
    )]
    public function getTracerOptions() {}

    #[OA\Get(
        path: "/api/v1/jobs",
        summary: "Eksplorasi Lowongan Pekerjaan",
        description: "Menyajikan katalog lowongan pekerjaan yang saat ini berstatus aktif. Mendukung param query pencarian secara opsional.",
        security: [["sanctum" => []]],
        tags: ["Job Vacancy"],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string"), description: "Kata kunci penelusuran (opsional)")
        ],
        responses: [
            new OA\Response(response: 200, description: "Katalog pekerjaan berhasil diambil dengan struktur paginasi.")
        ]
    )]
    public function getJobs() {}

    #[OA\Get(
        path: "/api/v1/jobs/applications",
        summary: "Riwayat Lamaran Saya",
        description: "Menampilkan log komprehensif atas semua lowongan yang pernah dilamar oleh alumni beserta representasi status terkininya.",
        security: [["sanctum" => []]],
        tags: ["Job Vacancy"],
        responses: [
            new OA\Response(response: 200, description: "Log histori lamaran didapatkan.")
        ]
    )]
    public function myApplications() {}

    #[OA\Get(
        path: "/api/v1/jobs/{id}",
        summary: "Rincian Lowongan Pekerjaan",
        description: "Mengambil deskripsi dan prasyarat utuh dari sebuah lowongan pekerjaan spesifik berdasarkan ID.",
        security: [["sanctum" => []]],
        tags: ["Job Vacancy"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Data detail lowongan sukses ditarik."),
            new OA\Response(response: 404, description: "Rekam jejak lowongan tidak ditemukan.")
        ]
    )]
    public function getJobDetail() {}

    #[OA\Post(
        path: "/api/v1/jobs/{id}/apply",
        summary: "Kirim Ajuan Lamaran Kerja",
        description: "Mendaftarkan aplikasi partisipasi pada sebuah lowongan. Menggunakan payload formulir untuk mengakomodasi pengunggahan berkas lamaran.",
        security: [["sanctum" => []]],
        tags: ["Job Vacancy"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "cv", type: "string", format: "binary", description: "Berkas Curiculum Vitae (PDF/DOCX), max 5MB"),
                        new OA\Property(property: "cover_letter", type: "string", description: "Surat pengantar lamaran (Opsional)", example: "Yth. HRD Manager, bersama ini saya sampaikan...")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Aplikasi lamaran sukses terekam dalam pangkalan data."),
            new OA\Response(response: 400, description: "Penolakan sistem karena alumni telah melakukan aplikasi sebelumnya pada loker ini.")
        ]
    )]
    public function applyJob() {}

    #[OA\Get(
        path: "/api/v1/events",
        summary: "Katalog Acara & Pelatihan",
        description: "Mengambil portofolio kegiatan atau acara bursa pelatihan yang diregistrasikan oleh instansi.",
        security: [["sanctum" => []]],
        tags: ["Events"],
        responses: [
            new OA\Response(response: 200, description: "Dataset katalog acara sukses dimuat dengan paginasi berurutan.")
        ]
    )]
    public function getEvents() {}

    #[OA\Get(
        path: "/api/v1/events/my-events",
        summary: "Riwayat Partisipasi Acara Saya",
        description: "Menampilkan rekam historis acara yang pernah diikuti maupun diregistrasikan oleh alumni terkait.",
        security: [["sanctum" => []]],
        tags: ["Events"],
        responses: [
            new OA\Response(response: 200, description: "Kumpulan histori partisipasi ditarik secara parsial.")
        ]
    )]
    public function getMyEvents() {}

    #[OA\Get(
        path: "/api/v1/events/{id}",
        summary: "Informasi Detil Acara",
        description: "Membedah representasi penuh dari deskripsi acara termasuk parameter geografis (lokasi) dan waktu pelaksanaan.",
        security: [["sanctum" => []]],
        tags: ["Events"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Detail spesifik memuat entitas objek acara tunggal.")
        ]
    )]
    public function getEventDetail() {}

    #[OA\Post(
        path: "/api/v1/events/{id}/register",
        summary: "Registrasi Kehadiran Acara",
        description: "Mem-validasi dan mendaftarkan alumni sebagai delegasi/partisipan ke dalam sesi acara yang dikelola BKK.",
        security: [["sanctum" => []]],
        tags: ["Events"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 201, description: "Hak kepesertaan terdaftar dengan sukses."),
            new OA\Response(response: 400, description: "Sistem menolak duplikasi pendaftaran kepesertaan.")
        ]
    )]
    public function registerEvent() {}
}
