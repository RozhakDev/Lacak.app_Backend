<x-mail::message>
Halo {{ $name }},

Terima kasih telah mendaftar di **Lacak.app**.

Gunakan kode verifikasi berikut untuk menyelesaikan proses verifikasi email Anda.

<x-mail::panel>
<div style="text-align:center;">
    <div style="font-size:32px;font-weight:700;letter-spacing:8px;">
        {{ $otpCode }}
    </div>
</div>
</x-mail::panel>

Kode verifikasi ini berlaku selama **5 menit**.

Jangan bagikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan **Lacak.app**.

Apabila Anda tidak melakukan pendaftaran akun, abaikan email ini.

Salam,<br>
**Tim IT {{ config('app.name') }}**
</x-mail::message>
