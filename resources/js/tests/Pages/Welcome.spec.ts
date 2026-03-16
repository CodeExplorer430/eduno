import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Welcome from '@/Pages/Welcome.vue';

// Stub Inertia components used in Welcome.vue
vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    usePage: (): { props: { auth: { user: null } } } => ({
        props: {
            auth: { user: null },
        },
    }),
}));

// Stub ApplicationLogo
vi.mock('@/Components/ApplicationLogo.vue', () => ({
    default: { template: '<svg aria-hidden="true"></svg>' },
}));

// Provide a minimal route() stub
beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string): string => `/${name}`;
});

describe('Welcome', () => {
    it('renders hero heading', () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: true, canRegister: true },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
        });
        expect(wrapper.find('h1').text()).toContain('Learn Smarter at UCC');
    });

    it('renders Log in link when canLogin is true', () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: true, canRegister: false },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
        });
        expect(wrapper.html()).toContain('Log in');
    });

    it('renders Register link when canRegister is true', () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: true, canRegister: true },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
        });
        expect(wrapper.html()).toContain('Register');
    });

    it('does not render auth links when canLogin is false', () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: false, canRegister: false },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
        });
        expect(wrapper.html()).not.toContain('Log in');
        expect(wrapper.html()).not.toContain('Register');
    });

    it('has a skip-to-main-content link', () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: true, canRegister: true },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
        });
        const skipLink = wrapper.find('a[href="#main-content"]');
        expect(skipLink.exists()).toBe(true);
        expect(skipLink.text()).toContain('Skip to main content');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Welcome, {
            props: { canLogin: true, canRegister: true },
            global: {
                mocks: {
                    $page: { props: { auth: { user: null } } },
                    route: (name: string) => `/${name}`,
                },
            },
            attachTo: document.body,
        });
        const results = await axe(wrapper.element as Element);
        expect(results.violations).toHaveLength(0);
    });
});
