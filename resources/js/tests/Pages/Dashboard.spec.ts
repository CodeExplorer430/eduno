import { describe, it, expect, vi, beforeAll, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Dashboard from '@/Pages/Dashboard.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    usePage: () => ({
        props: { auth: { user: { name: 'Test User', email: 'test@example.com' } } },
    }),
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: {
        template: '<div><slot name="header" /><main id="main-content"><slot /></main></div>',
    },
}));

vi.mock('@/Components/DeadlineItem.vue', () => ({
    default: {
        props: ['assignment'],
        template: '<li role="listitem">{{ assignment.title }}</li>',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string): string => `/${name}`;
});

beforeEach(() => {
    localStorage.clear();
});

describe('Dashboard', () => {
    it('renders the dashboard heading', () => {
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.html()).toContain('Dashboard');
    });

    it("renders the What's Next section", () => {
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.html()).toContain("What's Next");
    });

    it('renders upcoming deadlines when provided', () => {
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [
                    {
                        id: 1,
                        title: 'Lab Report 1',
                        course_name: 'CS101',
                        due_at: new Date(Date.now() + 86400000).toISOString(),
                    },
                ],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.html()).toContain('Lab Report 1');
    });

    it('shows welcome banner when not dismissed', () => {
        localStorage.clear();
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.html()).toContain('Welcome to Eduno');
    });

    it('hides welcome banner after dismiss click', async () => {
        localStorage.clear();
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.html()).toContain('Welcome to Eduno');
        await wrapper.find('button[aria-label="Dismiss welcome banner"]').trigger('click');
        expect(wrapper.html()).not.toContain('Welcome to Eduno');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Dashboard, {
            props: {
                upcoming: [],
                recentGrades: [],
                courseSummary: [],
            },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
            attachTo: document.body,
        });
        const results = await axe(wrapper.element as Element);
        expect(results.violations).toHaveLength(0);
    });
});
