<x-mail::message>
Halo {{ $name }},

Sistem keamanan kami mendeteksi adanya permintaan untuk mengatur ulang kata sandi (*reset password*) untuk akun Anda di **Lacak.app**.

Untuk melanjutkan proses pemulihan akses, silakan gunakan **6-digit Kode Verifikasi (OTP)** berikut:

<x-mail::panel>
<div style="text-align: center; font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #e53e3e;">
{{ $otpCode }}
</div>
</x-mail::panel>

*Catatan Keamanan:*
- Kode OTP ini hanya berlaku selama **5 menit**.
- Jangan pernah memberikan kode OTP ini kepada siapa pun, termasuk pihak yang mengatasnamakan administrator Lacak.app.

Apabila Anda tidak merasa meminta reset password, abaikan email ini. Akun Anda tetap aman.

Salam hangat,<br>
**Tim IT {{ config('app.name') }}**
</x-mail::message>
