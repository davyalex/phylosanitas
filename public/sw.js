/**
 * PhyloSanitas — Service worker (application installable / PWA)
 *
 * - Pages : réseau d'abord, copie locale si pas de connexion, sinon page « hors connexion ».
 * - CSS, JS, images, polices : copie locale servie tout de suite, mise à jour en arrière-plan.
 * - Jamais mis en cache : administration, connexion, formulaires (POST), publicités, statistiques.
 *
 * Changer VERSION force la mise à jour des fichiers en cache chez tous les visiteurs.
 */
const VERSION = 'ps-v2';
const STATIC_CACHE = `${VERSION}-static`;
const PAGES_CACHE = `${VERSION}-pages`;
const OFFLINE_URL = '/hors-ligne';
const MAX_PAGES = 40;

const PRECACHE = [
  OFFLINE_URL,
  '/assets_site/css/site.css',
  '/assets_site/js/site.js',
  '/assets_site/css/site-services.css',
  '/assets_site/js/site-services.js',
  '/assets_site/img/android-chrome-192x192.png',
  '/assets_site/img/logo/logo_tp.png',
];

const NEVER_CACHE = [/^\/admin/, /^\/login/, /^\/logout/, /^\/pub\//, /^\/popup\//, /^\/sondage\//, /^\/comment/, /^\/newsletter/, /^\/recherche/, /^\/sitemap\.xml/, /^\/robots\.txt/, /^\/push\//];
const THIRD_PARTY_SKIP = /googlesyndication|googletagmanager|google-analytics|doubleclick|adservice/;
const CACHEABLE_CDN = /fonts\.googleapis\.com|fonts\.gstatic\.com|cdn\.jsdelivr\.net/;

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => cache.addAll(PRECACHE.map((url) => new Request(url, { cache: 'reload' }))))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

async function trimCache(name, max) {
  const cache = await caches.open(name);
  const keys = await cache.keys();
  for (let i = 0; i < keys.length - max; i++) await cache.delete(keys[i]);
}

// Pages : réseau d'abord
async function handlePage(request) {
  try {
    const response = await fetch(request);
    if (response.ok && response.type === 'basic') {
      const copy = response.clone();
      caches.open(PAGES_CACHE).then((cache) => cache.put(request, copy)).then(() => trimCache(PAGES_CACHE, MAX_PAGES));
    }
    return response;
  } catch (e) {
    const cached = await caches.match(request, { ignoreSearch: false });
    return cached || (await caches.match(OFFLINE_URL)) || Response.error();
  }
}

// Fichiers statiques : copie locale tout de suite + mise à jour en arrière-plan
async function handleAsset(request, event) {
  const cache = await caches.open(STATIC_CACHE);
  const cached = await cache.match(request);
  const network = fetch(request)
    .then((response) => {
      if (response && (response.ok || response.type === 'opaque')) cache.put(request, response.clone());
      return response;
    })
    .catch(() => cached);
  if (cached) {
    event.waitUntil(network);
    return cached;
  }
  return network;
}

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);

  if (url.origin !== self.location.origin) {
    if (THIRD_PARTY_SKIP.test(url.hostname)) return;
    if (CACHEABLE_CDN.test(url.hostname)) event.respondWith(handleAsset(request, event));
    return;
  }

  if (NEVER_CACHE.some((re) => re.test(url.pathname))) return;

  if (request.mode === 'navigate') {
    event.respondWith(handlePage(request));
    return;
  }

  if (/^\/(assets_site|storage|vendor)\//.test(url.pathname) || /\.(css|js|woff2?|png|jpe?g|webp|svg|gif|ico)$/.test(url.pathname)) {
    event.respondWith(handleAsset(request, event));
  }
});

/* ---------- Notifications (Web Push) ----------
 * Le serveur envoie une notification sans contenu : on lit la dernière sur /push/derniere puis on l'affiche. */
self.addEventListener('push', (event) => {
  event.waitUntil((async () => {
    let data = null;
    try {
      const res = await fetch('/push/derniere', { cache: 'no-store', credentials: 'omit' });
      if (res.ok) data = await res.json();
    } catch (e) { /* hors connexion : message générique */ }
    data = data || { titre: 'PhyloSanitas', corps: 'De nouveaux conseils santé vous attendent.', url: '/', tag: 'ps' };

    await self.registration.showNotification(data.titre, {
      body: data.corps || '',
      icon: '/assets_site/img/android-chrome-192x192.png',
      badge: '/assets_site/img/favicon-32x32.png',
      tag: data.tag,
      lang: 'fr',
      data: { url: data.url || '/' },
    });
  })());
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const target = new URL(event.notification.data && event.notification.data.url || '/', self.location.origin);
  target.searchParams.set('source', 'notification');

  event.waitUntil((async () => {
    const wins = await clients.matchAll({ type: 'window', includeUncontrolled: true });
    for (const w of wins) {
      if (new URL(w.url).origin === target.origin && 'navigate' in w) {
        await w.focus();
        return w.navigate(target.href);
      }
    }
    return clients.openWindow(target.href);
  })());
});
