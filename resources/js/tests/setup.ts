import { vi, expect } from 'vitest';
import { config } from '@vue/test-utils';
// eslint-disable-next-line @typescript-eslint/no-require-imports, @typescript-eslint/no-explicit-any
const { toHaveNoViolations } = require('vitest-axe/matchers') as Record<string, any>;

expect.extend({ toHaveNoViolations });

// Make route() and $t() available in every component template
config.global.mocks = {
    route: vi.fn((): string => '/mock-route'),
    $t: (key: string): string => key,
};

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<slot />' },
    Link: { template: '<a><slot /></a>' },
    useForm: (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        wasSuccessful: false,
        hasErrors: false,
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
        get: vi.fn(),
        reset: vi.fn(),
        clearErrors: vi.fn(),
        setError: vi.fn(),
    }),
    usePage: (): Record<string, unknown> => ({
        props: {},
        url: '/',
        component: '',
        version: null,
    }),
    router: {
        visit: vi.fn(),
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
    },
}));

vi.stubGlobal('route', () => '/mock-route');

vi.mock('vue-i18n', () => ({
    createI18n: (): { install: ReturnType<typeof vi.fn> } => ({ install: vi.fn() }),
    useI18n: (): { t: (key: string) => string; locale: { value: string } } => ({
        t: (key: string): string => key,
        locale: { value: 'en' },
    }),
}));
