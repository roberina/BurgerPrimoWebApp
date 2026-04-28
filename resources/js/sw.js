import { precacheAndRoute } from 'workbox-precaching';

precacheAndRoute(self.__WB_MANIFEST);

self.addEventListener('push', (event) => {
    const data = event.data?.json() ?? {};
    const title = data.title ?? 'Burger Primo';
    const options = {
        body: data.body ?? '',
        icon: '/web-app-manifest-192x192.png',
        badge: '/web-app-manifest-192x192.png',
        data: { url: data.url ?? '/' },
        tag: 'announcement',
        renotify: true,
        vibrate: [200, 100, 200],
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url ?? '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((cs) => {
            const existing = cs.find((c) => 'focus' in c);
            if (existing) {
                existing.navigate(url);
                return existing.focus();
            }
            return clients.openWindow(url);
        })
    );
});
