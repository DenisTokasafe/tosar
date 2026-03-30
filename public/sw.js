const CACHE_NAME = 'v1.0.0';

const cacheAssets = [
    '/favicon.ico',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            cache.addAll(cacheAssets);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keyList => {
            return Promise.all(keyList.map(key => {
                if (key !== CACHE_NAME) {
                    return caches.delete(key);
                }
            }));
        })
    );
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request).then(response => {
            // 1. Jika ada di cache, langsung kembalikan
            if (response) {
                return response;
            }

            // 2. Jika tidak ada, coba ambil dari jaringan
            return fetch(event.request).catch(() => {
                /* PERBAIKAN DI SINI:
                   Jika network gagal dan tidak ada di cache,
                   kita HARUS mengembalikan sesuatu yang bertipe Response.
                */

                // Opsi A: Berikan pesan teks sederhana
                return new Response('Koneksi terputus. Halaman tidak tersedia offline.', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({ 'Content-Type': 'text/plain' })
                });

                // Opsi B: Jika kamu punya file offline.html di cacheAssets:
                // return caches.match('/offline.html');
            });
        })
    );
});
