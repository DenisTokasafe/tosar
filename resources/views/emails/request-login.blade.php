<x-mail::message>
# Request Pembuatan Akun

Halo Admin,

Seseorang dengan alamat email **{{ $email }}** telah mengajukan permintaan untuk pembuatan akun user login baru di sistem.

<x-mail::button :url="config('app.url') . '/admin/users/create?email=' . $email">
Proses Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
