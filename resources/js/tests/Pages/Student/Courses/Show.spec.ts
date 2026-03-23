import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Courses/Show.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    router: { get: vi.fn() },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    Breadcrumb: true,
    AssignmentCard: { template: '<div />', props: ['assignment'] },
};

const section = {
    id: 1,
    section_name: 'A',
    schedule_text: null,
    course: { id: 1, code: 'CCS123', title: 'Intro to HCI', description: null },
    instructor: { id: 2, name: 'Dr. Smith', email: 'smith@example.com' },
    modules: [],
    enrollments: [],
};

const moduleWithLesson = {
    id: 5,
    title: 'Week 1',
    order_no: 1,
    published_at: '2026-01-01',
    lessons: [{ id: 10, title: 'Intro', type: 'text', published_at: null }],
};

const routeMock = vi.fn(() => '/');

const baseProps = {
    section,
    announcements: [],
    assignments: [],
};

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Student/Courses/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: baseProps,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('course info section has aria-labelledby="course-info-heading"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: baseProps,
        });
        expect(wrapper.find('section[aria-labelledby="course-info-heading"]').exists()).toBe(true);
    });

    it('modules heading is present inside classwork tab', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: baseProps,
        });
        expect(wrapper.html()).toContain('Course Modules');
    });

    it('shows "No modules have been published" text when modules is empty', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: baseProps,
        });
        expect(wrapper.text()).toContain('No modules have been published');
    });

    it('lessons ul has aria-label="Lessons" when module has lessons', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { ...baseProps, section: { ...section, modules: [moduleWithLesson] } },
        });
        expect(wrapper.find('ul[aria-label="Lessons"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ShowPage, {
            props: baseProps,
            global: {
                mocks: { route: routeMock },
                stubs,
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
