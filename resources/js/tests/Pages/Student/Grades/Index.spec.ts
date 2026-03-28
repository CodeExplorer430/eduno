import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Student/Grades/Index.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    EmptyState: {
        template: '<div role="status">{{ title }}</div>',
        props: ['title', 'description', 'icon'],
    },
};

const globalOpts = { stubs };

const gradeFixture = {
    id: 1,
    score: 85,
    feedback: 'Good',
    released_at: '2026-01-01T00:00:00Z',
    submission: {
        assignment: {
            title: 'Lab Report 1',
            max_score: 100,
            course_section: {
                section_name: 'A',
                course: { title: 'HCI' },
            },
        },
    },
};

const props = { grades: [gradeFixture] };
const emptyProps = { grades: [] };

describe('Student/Grades/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows assignment title', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Lab Report 1');
    });

    it('shows score out of max', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('85');
    });

    it('shows "No grades available yet" when grades is empty', () => {
        const wrapper = mount(IndexPage, { props: emptyProps, global: globalOpts });
        expect(wrapper.text()).toContain('No grades available yet');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
