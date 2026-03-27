import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import LessonsIndex from '@/Pages/Instructor/Lessons/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Breadcrumb: { template: '<nav />' },
    EmptyState: { template: '<div data-testid="empty-state"><slot /></div>' },
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

const baseModule = { id: 10, title: 'Week 1: Introduction' };

const baseLessons = [
    {
        id: 1,
        title: 'What is HCI?',
        type: 'text',
        order_no: 1,
        published_at: '2026-03-01T00:00:00Z',
    },
    { id: 2, title: 'Video Overview', type: 'video', order_no: 2, published_at: null },
];

const baseProps = {
    section: baseSection,
    module: baseModule,
    lessons: baseLessons,
};

describe('Instructor/Lessons/Index', () => {
    it('renders module title in heading', () => {
        const wrapper = mount(LessonsIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('Week 1: Introduction');
    });

    it('renders section info (course code and section name)', () => {
        const wrapper = mount(LessonsIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('CCS101');
        expect(wrapper.text()).toContain('Section A');
    });

    it('renders lesson rows with title and type badge', () => {
        const wrapper = mount(LessonsIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('What is HCI?');
        expect(wrapper.text()).toContain('text');
        expect(wrapper.text()).toContain('Video Overview');
        expect(wrapper.text()).toContain('video');
    });

    it('renders Published badge for published lesson and Draft for unpublished', () => {
        const wrapper = mount(LessonsIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('Published');
        expect(wrapper.text()).toContain('Draft');
    });

    it('edit link has correct aria-label', () => {
        const wrapper = mount(LessonsIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.find('a[aria-label="Edit What is HCI?"]').exists()).toBe(true);
    });

    it('renders empty state when lessons is empty', () => {
        const wrapper = mount(LessonsIndex, {
            props: { ...baseProps, lessons: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[data-testid="empty-state"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(LessonsIndex, {
            props: baseProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, {
            rules: { region: { enabled: false } },
        });
        expect(results).toHaveNoViolations();
    });
});
