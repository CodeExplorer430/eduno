import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';
import { resolve } from 'path';

export default defineConfig({
    test: {
        environment: 'jsdom',
        globals: true,
        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/js'),
            },
        },
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: 'resources/js/app.ts',
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            navigateFallback: null, // CRITICAL: do NOT intercept HTML navigations (Inertia SSR)
            workbox: {
                globPatterns: ['**/*.{js,css,woff2,woff,ttf}'],
                navigateFallback: null,
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/fonts\.bunny\.net\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'bunny-fonts',
                            expiration: { maxAgeSeconds: 31536000 },
                            cacheableResponse: { statuses: [0, 200] },
                        },
                    },
                    {
                        urlPattern: /\/api\/.*/,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            networkTimeoutSeconds: 5,
                        },
                    },
                    {
                        urlPattern: /\/storage\/resources\/.+\.(pdf|docx?|pptx?)/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'eduno-resources',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 7 * 24 * 60 * 60,
                            },
                            cacheableResponse: { statuses: [0, 200] },
                        },
                    },
                ],
            },
            manifest: {
                name: 'Eduno LMS',
                short_name: 'Eduno',
                description: 'University of Caloocan City Learning Management System',
                theme_color: '#4f46e5',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                scope: '/',
                start_url: '/',
                icons: [
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    {
                        src: '/icons/icon-maskable-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                    { src: '/icons/apple-touch-icon.png', sizes: '180x180', type: 'image/png' },
                ],
            },
            devOptions: { enabled: false }, // Keep SW off in dev to avoid HMR interference
        }),
    ],
});
