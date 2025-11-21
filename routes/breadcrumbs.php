<?php

use App\Models\Hazard;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

/*
|--------------------------------------------------------------------------
| Hazard Breadcrumbs
|--------------------------------------------------------------------------
| Pastikan nama breadcrumb sama dengan nama route agar mudah dipanggil.
| Nama breadcrumb boleh beda dari nama route, tapi sebaiknya konsisten.
*/

// Halaman daftar hazard
Breadcrumbs::for('hazard', function (Trail $trail) {
    // Ganti 'Home' dengan nama lain jika mau
    $trail->push('Hazard List', route('hazard'));
});

// Form create hazard
Breadcrumbs::for('hazard-form', function (Trail $trail) {
    $trail->parent('hazard');
    $trail->push('Create Hazard', route('hazard-form'));
});

// Detail (toleran: menerima model atau id)
Breadcrumbs::for('hazard-detail', function (Trail $trail, $hazard) {
    $trail->parent('hazard');

    // Jika $hazard adalah id (int/string) -> ambil model
    if (is_numeric($hazard) || is_string($hazard)) {
        $hazard = Hazard::find($hazard);
    }

    if ($hazard) {
        $title = "Detail #{$hazard->no_referensi }";
        $url = route('hazard-detail', $hazard);
    } else {
        // fallback bila model tidak ditemukan (mis. saat generate dipanggil sebelum load)
        $title = "Detail";
        $url = '#';
    }

    $trail->push($title, $url);
});
