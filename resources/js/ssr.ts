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
import en from '@/locales/en';
import fil from '@/locales/fil';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const messages = { en, fil };

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
