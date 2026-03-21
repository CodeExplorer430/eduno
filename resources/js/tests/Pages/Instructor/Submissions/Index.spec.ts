import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Instructor/Submissions/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    SubmissionRow: { template: '<tr><td>row</td></tr>' },
    Button: {
        template: '<button v-bind="$attrs"><slot /></button>',
        inheritAttrs: false,
    },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseAssignment = {
    id: 1,
    title: 'Lab Report',
    max_score: 100,
    course_section: { section_name: 'A', course: { title: 'Intro to HCI' } },
};

const baseSubmission = {
    id: 1,
    student: { id: 10, name: 'Alice' },
    submitted_at: '2026-03-01T10:00:00Z',
    is_late: false,
    attempt_no: 1,
    status: 'submitted',
    grade: null,
};

describe('Instructor/Submissions/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [] },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('assignment title is in h1', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [] },
            global: globalOpts,
        });
        expect(wrapper.find('h1').text()).toContain('Lab Report');
    });

    it('empty state has role="status" when submissions is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('table has aria-label="Student submissions" when submissions exist', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [baseSubmission] },
            global: globalOpts,
        });
        expect(wrapper.find('table[aria-label="Student submissions"]').exists()).toBe(true);
    });

    it('column headers use scope="col" when submissions exist', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [baseSubmission] },
            global: globalOpts,
        });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('export button has aria-label="Export submissions as CSV"', () => {
        const wrapper = mount(IndexPage, {
            props: { assignment: baseAssignment, submissions: [] },
            global: globalOpts,
        });
        expect(wrapper.find('button[aria-label="Export submissions as CSV"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { assignment: baseAssignment, submissions: [] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
