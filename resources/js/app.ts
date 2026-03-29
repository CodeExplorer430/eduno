import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import AnimateOnScroll from 'primevue/animateonscroll';
import Ripple from 'primevue/ripple';
import { i18nVue, loadLanguageAsync } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Resolve the initial locale from the Inertia page payload embedded in the
// HTML by the server. Falls back to 'en' if unavailable.
function getInitialLocale(): string {
    try {
        const el = document.getElementById('app');
        if (el) {
            const pageData = JSON.parse(el.dataset.page ?? '{}') as {
                props?: { locale?: string };
            };
            return pageData.props?.locale ?? 'en';
        }
    } catch {
        // ignore parse errors
    }
    return 'en';
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nVue, {
                shared: true,
                lang: getInitialLocale(),
                resolve: async (lang: string) => {
                    const langs = import.meta.glob('../../lang/php_*.json');
                    const loader = langs[`../../lang/php_${lang}.json`];
                    if (!loader) return { default: {} };
                    // PHP files are namespaced by filename (lang/en/app.php → keys prefixed 'app.').
                    // Strip the prefix so components use keys like 'nav.dashboard' directly.
                    // Must return { default: {...} } — the Vite module format laravel-vue-i18n expects.
                    const module = (await loader()) as { default: Record<string, string> };
                    const prefix = 'app.';
                    return {
                        default: Object.fromEntries(
                            Object.entries(module.default)
                                .filter(([k]) => k.startsWith(prefix))
                                .map(([k, v]) => [k.slice(prefix.length), v])
                        ),
                    };
                },
            })
            .use(PrimeVue, {
                theme: { preset: Aura, options: { darkModeSelector: '.dark' } },
                ripple: true,
            })
            .use(ToastService)
            .use(ConfirmationService)
            .directive('animateonscroll', AnimateOnScroll)
            .directive('ripple', Ripple)
            .mount(el);
    },
    progress: {
        color: '#2563EB',
    },
});

// Keep the active locale in sync with the user's saved preference on every
// Inertia navigation (handles language switches without a full page reload).
router.on('navigate', (event) => {
    const locale = (event.detail.page.props as { locale?: string }).locale;
    if (locale && (locale === 'en' || locale === 'fil')) {
        void loadLanguageAsync(locale);
    }
});
