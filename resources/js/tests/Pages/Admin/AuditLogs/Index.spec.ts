import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Admin/AuditLogs/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    router: { get: vi.fn() },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    Button: {
        template: '<button><slot>{{ label }}</slot></button>',
        props: ['label', 'severity', 'size'],
    },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

const logFixture = {
    id: 1,
    actor_id: 1,
    action: 'user.role_changed',
    entity_type: 'User',
    entity_id: 5,
    created_at: '2025-01-15T09:00:00Z',
    actor: { id: 1, name: 'Admin User', email: 'admin@example.com' },
    metadata: { from: 'student', to: 'instructor' },
};

const props = { logs: { data: [logFixture], links: [] }, filters: {} };

describe('Admin/AuditLogs/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('filter section has aria-label="Filter audit logs"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('section[aria-label="Filter audit logs"]').exists()).toBe(true);
    });

    it('table has aria-label="Audit log entries"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('table[aria-label="Audit log entries"]').exists()).toBe(true);
    });

    it('column headers use scope="col"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('<details> element renders in a row when the log has metadata', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('details').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
