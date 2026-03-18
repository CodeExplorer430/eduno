import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Instructor/Assignments/Index.vue';
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

const section = {
    id: 1,
    section_name: 'A',
    course: { id: 1, code: 'CCS123', title: 'Intro to HCI' },
};

const assignment = {
    id: 1,
    title: 'Lab Report',
    due_at: '2026-04-01T23:59:00Z',
    max_score: 100,
    allow_resubmission: false,
    published_at: null,
};

describe('Instructor/Assignments/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('empty state has role="status" when assignments is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('assignment list has role="list" when assignments exist', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('ul[role="list"]').exists()).toBe(true);
    });

    it('edit link has aria-label containing assignment title', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('a[aria-label="Edit Lab Report"]').exists()).toBe(true);
    });

    it('"New Assignment" link is present', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('New Assignment');
    });

    it('empty state is absent when assignments exist', () => {
        const wrapper = mount(IndexPage, {
            props: { section, assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { section, assignments: [] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
