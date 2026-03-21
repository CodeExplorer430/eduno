import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Student/Assignments/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    AssignmentCard: { template: '<div />' },
};

const globalOpts = { stubs };

const assignment = {
    id: 1,
    title: 'Lab Report',
    due_at: '2026-04-01T23:59:00Z',
    max_score: 100,
    course_section_id: 1,
};

describe('Student/Assignments/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('assignments grid has role="list" when assignments exist', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="list"]').exists()).toBe(true);
    });

    it('assignments grid has aria-label="Assignments" when assignments exist', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('[aria-label="Assignments"]').exists()).toBe(true);
    });

    it('each assignment item has role="listitem"', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [assignment] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="listitem"]').exists()).toBe(true);
    });

    it('empty state section has aria-label="Empty state" when assignments is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.find('section[aria-label="Empty state"]').exists()).toBe(true);
    });

    it('assignments grid is absent when assignments is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="list"]').exists()).toBe(false);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { assignments: [] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
