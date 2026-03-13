const CACHE_NAME = 'e-barangay-cache-v3';
const ASSETS_TO_CACHE = [
    '/',
    '/css/bootstrap-icons.css',
    '/css/fonts/bootstrap-icons.woff',
    '/css/fonts/bootstrap-icons.woff2',
    '/js/tailwindcss.js',
    '/js/axios.js',
    '/js/alpine.js',
    '/js/fullcalendar.js',
    '/assets/images/LOGO (2).png',
    '/assets/images/hero-doctor.jpg',
    '/offline',
    '/manifest.json'
];

// Install Event
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    
    // For navigation requests, try network first, then cache, then offline page
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Cache a copy of the page for offline viewing
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request)
                        .then(response => response || caches.match('/offline'));
                })
        );
        return;
    }

    // For static assets, use Cache-First strategy
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                });
        })
    );
});