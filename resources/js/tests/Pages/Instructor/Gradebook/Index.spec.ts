import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import GradebookIndex from '@/Pages/Instructor/Gradebook/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Breadcrumb: { template: '<nav />' },
    Head: true,
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseSection = {
    id: 1,
    section_name: 'A',
    course: { id: 1, code: 'CCS101', title: 'Intro to HCI' },
};

const baseAssignments = [
    { id: 1, title: 'HW 1', max_score: 100, due_at: '2026-04-01T23:59:00Z' },
    { id: 2, title: 'Quiz 1', max_score: 50, due_at: '2026-04-10T23:59:00Z' },
];

const baseStudents = [
    {
        id: 10,
        name: 'Ana Reyes',
        grades: {
            1: { score: 92, released: true },
            2: null,
        },
        total: 92,
    },
    {
        id: 11,
        name: 'Ben Santos',
        grades: {
            1: { score: 78, released: false },
            2: { score: 45, released: true },
        },
        total: 123,
    },
];

const baseAverages: Record<number, number | null> = { 1: 85.0, 2: 45.0 };

const baseProps = {
    section: baseSection,
    assignments: baseAssignments,
    students: baseStudents,
    averages: baseAverages,
};

describe('Instructor/Gradebook/Index', () => {
    it('renders section name and course code in heading', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('CCS101');
        expect(wrapper.text()).toContain('Section A');
    });

    it('renders assignment columns with max_score', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('HW 1');
        expect(wrapper.text()).toContain('/100');
        expect(wrapper.text()).toContain('Quiz 1');
        expect(wrapper.text()).toContain('/50');
    });

    it('renders a dash for a null grade cell', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('—');
    });

    it('renders score for a graded cell', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('92');
        expect(wrapper.text()).toContain('78');
    });

    it('renders averages in tfoot', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        const tfoot = wrapper.find('tfoot');
        expect(tfoot.exists()).toBe(true);
        expect(tfoot.text()).toContain('85');
        expect(tfoot.text()).toContain('45');
    });

    it('export link has correct aria-label', () => {
        const wrapper = mount(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        expect(wrapper.find('a[aria-label="Export gradebook as CSV"]').exists()).toBe(true);
    });

    it('shows empty state when students list is empty', () => {
        const wrapper = mount(GradebookIndex, {
            props: { ...baseProps, students: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('No enrolled students yet.');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(GradebookIndex, {
            props: baseProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, {
            rules: { region: { enabled: false } },
        });
        expect(results).toHaveNoViolations();
    });
});
