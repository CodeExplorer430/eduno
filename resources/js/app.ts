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
import { createI18n } from 'vue-i18n';
import type { Ref } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Eagerly bundle both locale JSONs into the main chunk — no async loading, no race conditions.
const rawLocales = import.meta.glob('../../lang/php_*.json', { eager: true }) as Record<
    string,
    { default: Record<string, string> }
>;

// Convert flat 'app.nav.dashboard' keys → nested { nav: { dashboard: '...' } }
type NestedMessages = { [key: string]: string | NestedMessages };

function buildMessages(raw: Record<string, string>): NestedMessages {
    const prefix = 'app.';
    const result: NestedMessages = {};
    for (const [key, value] of Object.entries(raw)) {
        if (!key.startsWith(prefix)) continue;
        const parts = key.slice(prefix.length).split('.');
        let cur: NestedMessages = result;
        for (let i = 0; i < parts.length - 1; i++) {
            cur[parts[i]] ??= {};
            cur = cur[parts[i]] as NestedMessages;
        }
        cur[parts[parts.length - 1]] = value;
    }
    return result;
}

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

const i18n = createI18n({
    legacy: false as const,
    locale: getInitialLocale(),
    fallbackLocale: 'en',
    messages: {
        en: buildMessages(rawLocales['../../lang/php_en.json']?.default ?? {}),
        fil: buildMessages(rawLocales['../../lang/php_fil.json']?.default ?? {}),
    },
});

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
            .use(i18n)
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
        (i18n.global.locale as Ref<string>).value = locale;
    }
});
