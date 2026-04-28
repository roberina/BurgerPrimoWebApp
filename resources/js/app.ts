import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import Lenis from 'lenis'

const lenis = new Lenis({
  duration: 1.5,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  smoothWheel: true,
  prevent: (node) => node.closest('[data-lenis-prevent]') !== null,

})
;(window as any).lenis = lenis

function raf(time: number) {
  lenis.raf(time)
  requestAnimationFrame(raf)
}

requestAnimationFrame(raf)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// Register service worker + handle push subscriptions
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/build/sw.js').then(async (registration) => {
        if (!('PushManager' in window)) return;

        // Re-use existing subscription or skip if permission denied
        if (Notification.permission === 'denied') return;

        const existing = await registration.pushManager.getSubscription();
        if (existing) {
            // Already subscribed — ensure backend has it
            sendSubscriptionToServer(existing);
            return;
        }

        // Ask for permission once (after brief delay so page settles)
        setTimeout(async () => {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return;
            try {
                const sub = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(
                        import.meta.env.VITE_VAPID_PUBLIC_KEY as string
                    ),
                });
                sendSubscriptionToServer(sub);
            } catch (_) { /* user denied or browser blocked */ }
        }, 4000);
    });
}

function urlBase64ToUint8Array(base64String: string): Uint8Array {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
}

function sendSubscriptionToServer(sub: PushSubscription): void {
    const key = sub.getKey('p256dh');
    const auth = sub.getKey('auth');
    if (!key || !auth) return;
    fetch('/api/push/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            endpoint:   sub.endpoint,
            public_key: btoa(String.fromCharCode(...new Uint8Array(key))),
            auth_token: btoa(String.fromCharCode(...new Uint8Array(auth))),
        }),
    });
}
