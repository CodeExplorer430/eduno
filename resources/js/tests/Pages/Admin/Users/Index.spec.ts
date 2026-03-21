import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Admin/Users/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const usersFixture = {
    data: [
        {
            id: 1,
            name: 'Alice Admin',
            email: 'alice@test.com',
            role: 'admin',
            created_at: '2026-01-01T00:00:00Z',
        },
        {
            id: 2,
            name: 'Bob Student',
            email: 'bob@test.com',
            role: 'student',
            created_at: '2026-02-01T00:00:00Z',
        },
    ],
    links: [
        { url: null, label: '&laquo; Previous', active: false },
        { url: '/admin/users?page=1', label: '1', active: true },
        { url: '/admin/users?page=2', label: '2', active: false },
        { url: '/admin/users?page=2', label: 'Next &raquo;', active: false },
    ],
};

describe('Admin/Users/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('table has aria-label="Registered users"', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        expect(wrapper.find('table[aria-label="Registered users"]').exists()).toBe(true);
    });

    it('column headers use scope="col"', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('edit link has aria-label containing user name', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        expect(wrapper.find('a[aria-label="Edit Alice Admin"]').exists()).toBe(true);
    });

    it('<time> element exists with datetime attribute matching created_at', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        const time = wrapper.find('time');
        expect(time.exists()).toBe(true);
        expect(time.attributes('datetime')).toBe('2026-01-01T00:00:00Z');
    });

    it('pagination nav has aria-label="Pagination" when links > 3', () => {
        const wrapper = mount(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        expect(wrapper.find('nav[aria-label="Pagination"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { users: usersFixture },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
