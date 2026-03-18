import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Instructor/Courses/Index.vue';
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
    Tag: { template: '<span>{{ value }}</span>', props: ['value', 'severity'] },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const courseFixture = {
    id: 1,
    code: 'CCS123',
    title: 'Intro to HCI',
    status: 'active',
    sections: [{ id: 10, section_name: 'A', course_id: 1 }],
};

describe('Instructor/Courses/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [] },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('table has aria-label="Instructor courses" when courses exist', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [courseFixture] },
            global: globalOpts,
        });
        expect(wrapper.find('table[aria-label="Instructor courses"]').exists()).toBe(true);
    });

    it('column headers use scope="col"', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [courseFixture] },
            global: globalOpts,
        });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('modules link has aria-label containing course title and section name', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [courseFixture] },
            global: globalOpts,
        });
        const link = wrapper.find('a[aria-label="Manage modules for Intro to HCI — A"]');
        expect(link.exists()).toBe(true);
    });

    it('edit course link has aria-label containing course title', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [courseFixture] },
            global: globalOpts,
        });
        const link = wrapper.find('a[aria-label="Edit course Intro to HCI"]');
        expect(link.exists()).toBe(true);
    });

    it('"No courses yet." text shown when courses is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { courses: [] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('No courses yet.');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { courses: [] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
