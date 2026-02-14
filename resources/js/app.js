import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// When session expires, Inertia receives a non-Inertia response (Blade login page).
// Force a full-page redirect instead of rendering it inside the SPA container.
router.on('invalid', (event) => {
    event.preventDefault();
    window.location.href = event.detail.response?.url || '/login';
});

createInertiaApp({
    title: (title) => `${title} - Stok GA`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
