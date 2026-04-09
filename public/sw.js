const CACHE_NAME = 'indoorb-v1';

// Assets to cache immediately on install
const STATIC_ASSETS = [
    '/',
    '/staff/dashboard',
    '/manifest.json',
    '/images/logo.png',
    '/images/icons/icon-192.png',
    '/images/icons/icon-512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
];

// ─── Install ────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.allSettled(
                STATIC_ASSETS.map(url => cache.add(url).catch(() => {}))
            );
        })
    );
});

// ─── Activate ───────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

// ─── Fetch Strategy ─────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, browser extensions, and Livewire/API calls
    if (request.method !== 'GET') return;
    if (url.pathname.startsWith('/livewire')) return;
    if (url.pathname.startsWith('/api')) return;
    if (url.pathname.startsWith('/broadcasting')) return;

    // Static assets (CSS, JS, images, fonts) — Cache First
    if (
        url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?|ttf)$/) ||
        url.hostname !== location.hostname
    ) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    }
                    return response;
                }).catch(() => cached);
            })
        );
        return;
    }

    // HTML pages — Network First, fall back to cache
    event.respondWith(
        fetch(request)
            .then(response => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request).then(cached => {
                if (cached) return cached;
                // Offline fallback page
                return caches.match('/offline.html');
            }))
    );
});

// ─── Push Notifications ─────────────────────────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'IndoorB', {
            body:    data.body    || 'You have a new notification',
            icon:    data.icon    || '/images/icons/icon-192.png',
            badge:   data.badge   || '/images/icons/icon-96.png',
            data:    data.url     || '/staff/dashboard',
            vibrate: [200, 100, 200],
            actions: data.actions || [],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (const client of list) {
                if (client.url && 'focus' in client) return client.focus();
            }
            return clients.openWindow(event.notification.data || '/staff/dashboard');
        })
    );
});
