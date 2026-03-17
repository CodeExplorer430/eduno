import { vi, expect } from 'vitest';
// eslint-disable-next-line @typescript-eslint/no-require-imports, @typescript-eslint/no-explicit-any
const { toHaveNoViolations } = require('vitest-axe/matchers') as Record<string, any>;

expect.extend({ toHaveNoViolations });

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
