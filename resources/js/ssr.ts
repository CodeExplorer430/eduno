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
import { i18nVue } from 'laravel-vue-i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

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
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(i18nVue, {
                    shared: true,
                    lang: locale,
                    resolve: async (lang: string) => {
                        const langs = import.meta.glob('../../lang/php_*.json');
                        const loader = langs[`../../lang/php_${lang}.json`];
                        if (!loader) return { default: {} };
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
                .directive('ripple', Ripple);
        },
    })
);
