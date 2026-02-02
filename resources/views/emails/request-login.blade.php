<x-mail::message>
# Request Pembuatan Akun

Halo Admin,

Seseorang dengan alamat email **{{ $email }}** telah mengajukan permintaan untuk pembuatan akun user login baru di sistem.

<x-mail::button :url="route('people')">
Proses Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
