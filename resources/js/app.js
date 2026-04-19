import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // ✅ CREATE PINIA INSTANCE
        const pinia = createPinia();

        app
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia); // ✅ REGISTER PINIA

        // ✅ OPTIONAL: initialize theme here
        // import your store AFTER pinia is created
        // const themeStore = useThemeStore();
        // themeStore.initTheme();

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
