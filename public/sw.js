// Service Worker for Lola's Kusina PWA
const CACHE_NAME = 'lolas-kusina-v2';
const STATIC_ASSETS = [
    '/css/styles.css',
    '/images/logo.png'
];

// Install event - cache only static assets
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Opened cache');
                return cache.addAll(STATIC_ASSETS).catch(function(err) {
                    console.log('Cache addAll failed:', err);
                });
            })
    );
    self.skipWaiting();
});

// Fetch event - network-first for HTML/PHP, cache-first for static assets
self.addEventListener('fetch', function(event) {
    var url = new URL(event.request.url);
    var isHtml = event.request.headers.get('Accept') && event.request.headers.get('Accept').includes('text/html');
    var isStatic = /\.(css|js|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf)$/.test(url.pathname);

    if (isHtml || !isStatic) {
        // Network-first for pages — always get fresh content
        event.respondWith(
            fetch(event.request).catch(function() {
                return caches.match(event.request);
            })
        );
    } else {
        // Cache-first for static assets (images, CSS, fonts)
        event.respondWith(
            caches.match(event.request).then(function(response) {
                if (response) {
                    return response;
                }
                return fetch(event.request).then(function(response) {
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }
                    var responseToCache = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(event.request, responseToCache);
                    });
                    return response;
                });
            })
        );
    }
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName !== CACHE_NAME;
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
    self.clients.claim();
});
