const CACHE_NAME = 'e-barangay-cache-v2';
const ASSETS_TO_CACHE = [
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
    // 1. Skip non-GET requests (POST, etc.)
    if (event.request.method !== 'GET') return;

    // 2. Skip dynamic routes that contain CSRF tokens or user-specific data
    const url = new URL(event.request.url);
    const skipPaths = [
        '/login', '/auth/login', '/register', '/logout', '/dashboard', 
        '/midwife/slots', '/doctor/health-records', '/patient/health-records',
        '/midwife/patients'
    ];
    
    // Bypass cache for ALL navigation requests to ensure fresh HTML
    if (event.request.mode === 'navigate' || url.pathname === '/' || skipPaths.some(path => url.pathname.startsWith(path))) {
        event.respondWith(
            fetch(event.request).catch(() => caches.match('/offline'))
        );
        return;
    }

    // 3. For static assets, use Cache-First strategy
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request)
                .then((networkResponse) => {
                    // Cache only valid successful responses
                    if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Fallback to offline page for navigation requests
                    if (event.request.mode === 'navigate') {
                        return caches.match('/offline');
                    }
                });
        })
    );
});