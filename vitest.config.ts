import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [vue()],
    test: {
        globals: true,
        environment: 'happy-dom',
        setupFiles: ['./resources/js/tests/setup.ts'],
        exclude: ['**/node_modules/**', 'e2e/**'],
    },
    resolve: {
        alias: { '@': resolve(__dirname, 'resources/js') },
    },
});
