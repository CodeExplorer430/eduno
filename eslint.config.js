import pluginVue from 'eslint-plugin-vue';
import tsParser from '@typescript-eslint/parser';
import tsPlugin from '@typescript-eslint/eslint-plugin';
import vueParser from 'vue-eslint-parser';
import prettierConfig from 'eslint-config-prettier';

export default [
    // TypeScript files
    {
        files: ['resources/js/**/*.ts'],
        languageOptions: { parser: tsParser, parserOptions: { project: true } },
        plugins: { '@typescript-eslint': tsPlugin },
        rules: {
            ...tsPlugin.configs['recommended'].rules,
            '@typescript-eslint/no-explicit-any': 'error',
            '@typescript-eslint/explicit-function-return-type': 'warn',
        },
    },
    // Vue files
    {
        files: ['resources/js/**/*.vue'],
        languageOptions: {
            parser: vueParser,
            parserOptions: { parser: tsParser, extraFileExtensions: ['.vue'] },
        },
        plugins: { vue: pluginVue, '@typescript-eslint': tsPlugin },
        rules: {
            ...pluginVue.configs['recommended'].rules,
            'vue/multi-word-component-names': 'off',
            'vue/require-default-prop': 'off',
        },
    },
    // Pagination uses v-html for Laravel's trusted HTML labels (e.g. &laquo; &raquo;)
    {
        files: ['resources/js/Components/Pagination.vue'],
        rules: { 'vue/no-v-html': 'off' },
    },
    // Prettier overrides last
    prettierConfig,
    { ignores: ['vendor/', 'node_modules/', 'public/build/', 'bootstrap/ssr/'] },
];
