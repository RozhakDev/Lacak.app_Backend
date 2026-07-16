<x-mail::message>
Halo {{ $name }},

Terima kasih telah mendaftar di portal alumni resmi **Lacak.app**. 

Sebagai langkah terakhir untuk mengaktifkan akun Anda dan menjaga keamanan data alumni, silakan masukkan **6-digit Kode Verifikasi (OTP)** di bawah ini pada halaman aplikasi:

<x-mail::panel>
<div style="text-align: center; font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #2d3748;">
{{ $otpCode }}
</div>
</x-mail::panel>

*Kode ini bersifat rahasia dan hanya berlaku selama 5 menit.* 

Jika Anda tidak merasa mendaftar di Lacak.app, Anda dapat mengabaikan email ini dengan aman.

Salam hangat,<br>
**Tim IT {{ config('app.name') }}**
</x-mail::message>
