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
    // 1. Filter: Hanya tangani request GET
    if (event.request.method !== 'GET') {
        return; // Biarkan browser menangani request POST/lainnya secara normal
    }

    event.respondWith(
        caches.open(CACHE_NAME).then(cache => {
            return cache.match(event.request).then(response => {
                // Jika ada di cache, kirim response. Jika tidak, fetch dari internet.
                return response || fetch(event.request).catch(() => {
                    // Opsional: Return halaman offline jika fetch gagal
                });
            });
        })
    );
});
