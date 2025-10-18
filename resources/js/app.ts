interface InertiaState {
    store?: {
        template?: string;
    };
}

declare global {
    interface Window {
        __INITIAL_STATE__: InertiaState;
    }
}

const loadTemplateCSS = async () => {
    const path = window.location.pathname;
    // console.log('Current path:', path);

    if (path.startsWith('/store/')) {
        try {
            const template =
                window.__INITIAL_STATE__?.store?.template || 'default';
            await import(`./pages/templates/${template}/app.css`);

            // console.log(`Loaded template CSS: ${template}`);

            return;
        } catch (error) {
            console.warn('Template CSS not found, falling back to default CSS');
            console.error(error);
        }
    }
    await import('../css/app.css');
};

loadTemplateCSS();

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import { Ziggy } from './ziggy.js';

if (typeof window !== 'undefined') {
    (window as any).Ziggy = Ziggy;
}

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
            .use(ZiggyVue, Ziggy as any)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
