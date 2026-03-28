import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AllPage from '@/Pages/Instructor/Submissions/All.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
    },
    mocks: { route: routeMock },
};

const submissionFixture = {
    id: 1,
    status: 'submitted',
    submitted_at: '2026-01-01T00:00:00Z',
    is_late: false,
    student: { id: 1, name: 'Alice' },
    assignment: {
        id: 1,
        title: 'Lab 1',
        max_score: 100,
        course_section: { section_name: 'A', course: { title: 'HCI' } },
    },
    grade: null,
};

const props = {
    submissions: {
        data: [submissionFixture],
        current_page: 1,
        last_page: 1,
        prev_page_url: null,
        next_page_url: null,
    },
};

describe('Instructor/Submissions/All', () => {
    it('renders without crashing', () => {
        const wrapper = mount(AllPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows student name in table', () => {
        const wrapper = mount(AllPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Alice');
    });

    it('table has aria-label', () => {
        const wrapper = mount(AllPage, { props, global: globalOpts });
        expect(wrapper.find('table[aria-label="All submissions"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(AllPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
