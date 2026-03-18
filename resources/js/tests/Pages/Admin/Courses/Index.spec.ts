import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Admin/Courses/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    router: { get: vi.fn(), patch: vi.fn() },
    usePage: vi.fn(() => ({ props: { flash: { success: 'Course status updated.' } } })),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    Tag: { template: '<span>{{ value }}</span>', props: ['value', 'severity'] },
    Select: { template: '<select v-bind="$attrs" />', inheritAttrs: false },
    Button: {
        template: '<button v-bind="$attrs"><slot>{{ label }}</slot></button>',
        inheritAttrs: false,
        props: ['label', 'severity', 'size'],
    },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

const courseFixture = {
    id: 1,
    code: 'CCS123',
    title: 'Intro to HCI',
    department: 'CCS',
    status: 'published' as const,
    sections: [{ id: 1 }],
};

const props = {
    courses: { data: [courseFixture], links: [] },
    filters: {},
    statuses: [
        { name: 'Draft', value: 'draft' },
        { name: 'Published', value: 'published' },
        { name: 'Archived', value: 'archived' },
    ],
};

describe('Admin/Courses/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('filter section has aria-label="Filter courses"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('section[aria-label="Filter courses"]').exists()).toBe(true);
    });

    it('table has aria-label="Course list"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('table[aria-label="Course list"]').exists()).toBe(true);
    });

    it('column headers use scope="col"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('flash message has role="status" and aria-live="polite"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const flash = wrapper.find('[role="status"][aria-live="polite"]');
        expect(flash.exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
