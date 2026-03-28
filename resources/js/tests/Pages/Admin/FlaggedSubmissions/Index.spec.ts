import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Admin/FlaggedSubmissions/Index.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

vi.mock('@/composables/useFormatDate', () => ({
    useFormatDate: () => ({ formatDate: (d: string) => d }),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Pagination: true,
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

const submissionFixture = {
    id: 1,
    submitted_at: '2026-01-10T10:00:00Z',
    is_late: false,
    student: { id: 1, name: 'Alice Santos' },
    assignment: {
        id: 1,
        title: 'Lab Report 1',
        course_section: { section_name: 'A', course: { title: 'HCI' } },
    },
};

const props = { submissions: { data: [submissionFixture], links: [] } };
const emptyProps = { submissions: { data: [], links: [] } };

describe('Admin/FlaggedSubmissions/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('table has aria-label="Flagged submissions"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('table[aria-label="Flagged submissions"]').exists()).toBe(true);
    });

    it('column headers use scope="col"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const headers = wrapper.findAll('th[scope="col"]');
        expect(headers.length).toBeGreaterThan(0);
    });

    it('View link has aria-label containing student name', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const link = wrapper.find('a[aria-label]');
        expect(link.attributes('aria-label')).toContain('Alice Santos');
    });

    it('shows student name in table row', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Alice Santos');
    });

    it('shows empty state when no submissions', () => {
        const wrapper = mount(IndexPage, { props: emptyProps, global: globalOpts });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('No submissions are currently flagged');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
