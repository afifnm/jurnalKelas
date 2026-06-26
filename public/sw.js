const CACHE_NAME = 'jurnal-kelas-v2';
const OFFLINE_URL = '/offline';

// Assets to pre-cache on install
const PRECACHE = [
    '/offline',
];

// ── Install: pre-cache shell ──────────────────────────────────────────────────
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME)
            .then(c => c.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

// ── Activate: clean old caches ────────────────────────────────────────────────
self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ── Fetch: network-first for HTML, cache-first for static assets ──────────────
self.addEventListener('fetch', (e) => {
    const req = e.request;

    // Skip non-GET and non-http(s)
    if (req.method !== 'GET') return;
    if (!req.url.startsWith('http')) return;

    // Skip API, admin actions, auth routes — always network
    const url = new URL(req.url);
    const skipPaths = ['/login', '/logout', '/admin', '/guru/jurnal', '/ks/validasi', '/lampiran'];
    if (skipPaths.some(p => url.pathname.startsWith(p) && req.method !== 'GET')) return;

    const isNavigation = req.mode === 'navigate';
    const isStaticAsset = /\.(css|js|woff2?|ttf|otf|png|jpg|jpeg|webp|svg|ico)(\?.*)?$/.test(url.pathname);

    if (isStaticAsset) {
        // Cache-first for static assets (fonts, CSS, JS, images)
        e.respondWith(
            caches.match(req).then(cached => {
                if (cached) return cached;
                return fetch(req).then(res => {
                    if (res.ok) {
                        const clone = res.clone();
                        caches.open(CACHE_NAME).then(c => c.put(req, clone));
                    }
                    return res;
                }).catch(() => cached || new Response('', { status: 503 }));
            })
        );
        return;
    }

    if (isNavigation) {
        // Network-first for page navigations → offline fallback
        e.respondWith(
            fetch(req)
                .then(res => {
                    // Cache successful navigation responses
                    if (res.ok && res.status < 300) {
                        const clone = res.clone();
                        caches.open(CACHE_NAME).then(c => c.put(req, clone));
                    }
                    return res;
                })
                .catch(() =>
                    caches.match(req)
                        .then(cached => cached || caches.match(OFFLINE_URL))
                )
        );
        return;
    }

    // Default: network with cache fallback
    e.respondWith(
        fetch(req).catch(() => caches.match(req))
    );
});
