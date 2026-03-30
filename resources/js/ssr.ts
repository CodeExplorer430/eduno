import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import AnimateOnScroll from 'primevue/animateonscroll';
import Ripple from 'primevue/ripple';
import { createI18n } from 'vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Eagerly bundle both locale JSONs — synchronous, no async chunk needed on server.
const rawLocales = import.meta.glob('../../lang/php_*.json', { eager: true }) as Record<
    string,
    { default: Record<string, string> }
>;

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

const messages = {
    en: buildMessages(rawLocales['../../lang/php_en.json']?.default ?? {}),
    fil: buildMessages(rawLocales['../../lang/php_fil.json']?.default ?? {}),
};

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob<DefineComponent>('./Pages/**/*.vue')
            ),
        setup({ App, props, plugin }) {
            const locale = (page.props as { locale?: string }).locale ?? 'en';
            const i18n = createI18n({
                legacy: false as const,
                locale,
                fallbackLocale: 'en',
                messages,
            });
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(i18n)
                .use(PrimeVue, {
                    theme: { preset: Aura, options: { darkModeSelector: '.dark' } },
                    ripple: true,
                })
                .use(ToastService)
                .use(ConfirmationService)
                .directive('animateonscroll', AnimateOnScroll)
                .directive('ripple', Ripple);
        },
    })
);
