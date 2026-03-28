import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Student/Courses/Index.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
}));

const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        CourseCard: { template: '<div><slot /></div>' },
        EmptyState: { template: '<div role="status"><slot /></div>' },
    },
    directives: {
        animateonscroll: {},
    },
};

const sectionFixture = {
    id: 1,
    section_name: 'A',
    schedule_text: 'MWF 9am',
    course: { id: 1, code: 'CCS123', title: 'HCI', status: 'published' },
    instructor: { id: 1, name: 'Dr. Smith' },
};

const props = { sections: [sectionFixture] };
const emptyProps = { sections: [] };

describe('Student/Courses/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('course list region has aria-label="Enrolled courses"', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.find('[aria-label="Enrolled courses"]').exists()).toBe(true);
    });

    it('renders empty state when sections is empty', () => {
        const wrapper = mount(IndexPage, { props: emptyProps, global: globalOpts });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
