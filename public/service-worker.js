const CACHE_PREFIX = 'feira-fabricas-cache';
// Bump version to invalidate old caches
const CACHE_VERSION = 'v2';
const CACHE_NAME = `${CACHE_PREFIX}-${CACHE_VERSION}`;

const CORE_ASSETS = [
  '/',
  '/android-chrome-192x192.png',
  '/android-chrome-512x512.png',
  '/favicon-32x32.png',
  '/favicon-16x16.png',
  '/apple-touch-icon.png',
  '/logo-ofc.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(CORE_ASSETS))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(key => key.startsWith(CACHE_PREFIX) && key !== CACHE_NAME)
          .map(key => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
    return;
  }
  const url = new URL(event.request.url);
  const acceptHeader = event.request.headers.get('accept') || '';

  // Never cache admin or API routes
  const isAdmin = url.pathname.startsWith('/admin');
  const isApi = url.pathname.startsWith('/api') || url.pathname.startsWith('/webhooks');

  // For HTML navigation requests, prefer network-first to avoid stale pages
  const isHtmlRequest = acceptHeader.includes('text/html');

  if (isAdmin || isApi) {
    event.respondWith(fetch(event.request).catch(() => caches.match(event.request)));
    return;
  }

  if (isHtmlRequest) {
    event.respondWith(
      fetch(event.request)
        .then(resp => {
          const clone = resp.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
          return resp;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }

  // Default: cache-first for static assets
  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      if (cachedResponse) return cachedResponse;
      return fetch(event.request).then(networkResponse => {
        if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
          return networkResponse;
        }
        // Skip chrome-extension requests
        if (event.request.url.startsWith('chrome-extension://')) {
          return networkResponse;
        }
        const responseToCache = networkResponse.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseToCache));
        return networkResponse;
      });
    })
  );
});

