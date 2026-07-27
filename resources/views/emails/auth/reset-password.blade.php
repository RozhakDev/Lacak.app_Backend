<x-mail::message>
Halo {{ $name }},

Kami menerima permintaan untuk mengatur ulang kata sandi akun **Lacak.app** Anda.

Gunakan kode verifikasi berikut untuk melanjutkan proses reset password.

<x-mail::panel>
<div style="text-align:center;">
    <div style="font-size:32px;font-weight:700;letter-spacing:8px;">
        {{ $otpCode }}
    </div>
</div>
</x-mail::panel>

Kode verifikasi ini berlaku selama **5 menit**.

Jangan bagikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan **Lacak.app**.

Apabila Anda tidak meminta reset password, abaikan email ini. Kata sandi Anda tidak akan berubah tanpa verifikasi menggunakan kode di atas.

Salam,<br>
**Tim IT {{ config('app.name') }}**
</x-mail::message>
