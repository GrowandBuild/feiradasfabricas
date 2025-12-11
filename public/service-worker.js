const CACHE_PREFIX = 'feira-fabricas-cache';
// Bump version to invalidate old caches
// v5: Added manifest caching and improved PWA support
const CACHE_VERSION = 'v5';
const CACHE_NAME = `${CACHE_PREFIX}-${CACHE_VERSION}`;

const CORE_ASSETS = [
  // Only cache static assets, never HTML pages
  '/android-chrome-192x192.png',
  '/android-chrome-512x512.png',
  '/favicon-32x32.png',
  '/favicon-16x16.png',
  '/apple-touch-icon.png',
  '/logo-ofc.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(CORE_ASSETS).catch(err => {
        console.warn('SW: Some assets failed to cache:', err);
      });
    })
  );
  self.skipWaiting(); // Ativar imediatamente
});

self.addEventListener('activate', event => {
  event.waitUntil(
    Promise.all([
      // Limpar caches antigos
      caches.keys().then(keys =>
        Promise.all(
          keys
            .filter(key => key.startsWith(CACHE_PREFIX) && key !== CACHE_NAME)
            .map(key => caches.delete(key))
        )
      ),
      // Tomar controle imediato de todas as páginas
      self.clients.claim()
    ])
  );
});

// Escutar mensagem para forçar ativação
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
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
  const isManifest = url.pathname === '/site.webmanifest';

  // For HTML navigation requests, NEVER cache - always fetch from server
  // This prevents serving stale pages when server is offline
  const isHtmlRequest = acceptHeader.includes('text/html');

  // Manifest deve sempre vir do servidor (network-first)
  if (isManifest) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
          }
          return response;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }

  if (isAdmin || isApi || isHtmlRequest) {
    // Always fetch from network, never serve from cache for HTML/admin/API
    event.respondWith(
      fetch(event.request).catch(() => {
        // If offline and it's HTML, show error instead of stale cache
        if (isHtmlRequest) {
          return new Response('Servidor offline. Por favor, verifique sua conexão.', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
          });
        }
        return caches.match(event.request);
      })
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

