const version = "2025-28-05-4"; // <-- à mettre à jour à chaque nouvelle version
const staticCacheName = `cache-${version}`;
const assets = ["/", "https://funlab.be/BeAware//home.html"];

// INSTALLATION
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(staticCacheName).then((cache) => {
      return cache.addAll(assets);
    }).then(() => self.skipWaiting()) // Force l'installation immédiate
  );
});

// ACTIVATION + SUPPRESSION DES ANCIENS CACHES
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys
          .filter((key) => key !== staticCacheName)
          .map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim()) // Prend le contrôle sans attendre le prochain reload
  );
});

// INTERCEPTION DES REQUÊTES
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      if (response) {
        return response;
      }

      const fetchRequest = event.request.clone();

      return fetch(fetchRequest).then((response) => {
        if (!response || response.status !== 200 || response.type !== "basic") {
          return response;
        }

        const responseToCache = response.clone();

        caches.open(staticCacheName).then((cache) => {
          cache.put(event.request, responseToCache);
        });

        return response;
      });
    })
  );
});
