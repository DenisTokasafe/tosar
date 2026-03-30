const CACHE_NAME = 'v1.0.0';

// Aset yang wajib ada agar PWA bisa berjalan (static assets)
const cacheAssets = [
    '/',
    '/favicon.ico',
    // Tambahkan build CSS/JS dari Vite di sini jika tidak menggunakan plugin otomatis
    // '/build/assets/app-xxxx.css',
];

// 1. INSTALL: Menyimpan aset statis ke cache
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('SW: Caching Assets');
                // Menggunakan Promise.allSettled agar jika satu file gagal (misal favicon 404),
                // proses install tetap berlanjut.
                return Promise.allSettled(
                    cacheAssets.map(asset => cache.add(asset))
                );
            })
            .then(() => self.skipWaiting()) // Memaksa SW baru segera aktif
    );
});

// 2. ACTIVATE: Menghapus cache lama jika versi (CACHE_NAME) berubah
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keyList => {
            return Promise.all(
                keyList.map(key => {
                    if (key !== CACHE_NAME) {
                        console.log('SW: Removing Old Cache', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim()) // Mengambil kendali halaman segera
    );
});

// 3. FETCH: Strategi Cache-First dengan Network Fallback
self.addEventListener('fetch', event => {
    // Filter: Hanya tangani request GET (Penting agar CSRF/POST Laravel tidak error)
    if (event.request.method !== 'GET') return;

    // Opsional: Lewati caching untuk route Laravel Admin atau API tertentu
    if (event.request.url.includes('/admin') || event.request.url.includes('/api')) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then(response => {
            // Jika ada di cache, gunakan cache.
            if (response) {
                return response;
            }

            // Jika tidak ada di cache, ambil dari network
            return fetch(event.request)
                .then(networkResponse => {
                    // Validasi response: Pastikan sukses dan tipenya basic (milik domain sendiri)
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                        return networkResponse;
                    }

                    // Kloning response karena response hanya bisa dipakai sekali
                    const responseToCache = networkResponse.clone();

                    caches.open(CACHE_NAME).then(cache => {
                        // Jangan simpan halaman yang dinamis (HTML), cukup aset statis jika diinginkan
                        // Namun untuk full PWA offline, simpan semuanya:
                        cache.put(event.request, responseToCache);
                    });

                    return networkResponse;
                })
                .catch(() => {
                    // Jika offline dan request adalah halaman (HTML), arahkan ke fallback jika perlu
                    if (event.request.headers.get('accept').includes('text/html')) {
                        return caches.match('/'); // Return root sebagai fallback offline
                    }
                });
        })
    );
});
