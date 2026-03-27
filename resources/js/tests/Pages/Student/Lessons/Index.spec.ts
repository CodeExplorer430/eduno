import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import LessonsIndex from '@/Pages/Student/Lessons/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    EmptyState: { template: '<div data-testid="empty-state"><slot /></div>' },
    Head: true,
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseLesson = {
    id: 1,
    title: 'What is HCI?',
    type: 'text',
    order_no: 1,
    module: {
        id: 10,
        title: 'Week 1: Introduction',
        section: {
            id: 1,
            section_name: 'A',
            course: { code: 'CCS101', title: 'Intro to HCI' },
        },
    },
};

const secondLesson = {
    id: 2,
    title: "Norman's Model",
    type: 'pdf',
    order_no: 2,
    module: {
        id: 20,
        title: 'Week 2: Design Models',
        section: {
            id: 2,
            section_name: 'B',
            course: { code: 'CCS102', title: 'UI Design' },
        },
    },
};

describe('Student/Lessons/Index', () => {
    it('renders group headers with course code and module title', () => {
        const wrapper = mount(LessonsIndex, {
            props: { lessons: [baseLesson, secondLesson] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('CCS101');
        expect(wrapper.text()).toContain('Week 1: Introduction');
        expect(wrapper.text()).toContain('CCS102');
        expect(wrapper.text()).toContain('Week 2: Design Models');
    });

    it('renders lesson title with view link', () => {
        const wrapper = mount(LessonsIndex, {
            props: { lessons: [baseLesson] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('What is HCI?');
        expect(wrapper.find('a[aria-label="View lesson: What is HCI?"]').exists()).toBe(true);
    });

    it('renders empty state when lessons is empty', () => {
        const wrapper = mount(LessonsIndex, {
            props: { lessons: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[data-testid="empty-state"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(LessonsIndex, {
            props: { lessons: [baseLesson] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, {
            rules: { region: { enabled: false } },
        });
        expect(results).toHaveNoViolations();
    });
});
