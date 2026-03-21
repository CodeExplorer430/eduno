import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Courses/Show.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    AssignmentCard: { template: '<div />', props: ['assignment'] },
};

const section = {
    id: 1,
    section_name: 'A',
    schedule_text: null,
    course: { id: 1, code: 'CCS123', title: 'Intro to HCI', description: null },
    instructor: { id: 2, name: 'Dr. Smith' },
    modules: [],
    assignments: [],
};

const moduleWithLesson = {
    id: 5,
    title: 'Week 1',
    order_no: 1,
    published_at: '2026-01-01',
    lessons: [{ id: 10, title: 'Intro', type: 'text', published_at: null }],
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Student/Courses/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { section },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('course info section has aria-labelledby="course-info-heading"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { section },
        });
        expect(wrapper.find('section[aria-labelledby="course-info-heading"]').exists()).toBe(true);
    });

    it('modules section has aria-labelledby="modules-heading"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { section },
        });
        expect(wrapper.find('section[aria-labelledby="modules-heading"]').exists()).toBe(true);
    });

    it('shows "No modules have been published" text when modules is empty', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { section },
        });
        expect(wrapper.text()).toContain('No modules have been published');
    });

    it('lessons ul has aria-label="Lessons" when module has lessons', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { section: { ...section, modules: [moduleWithLesson] } },
        });
        expect(wrapper.find('ul[aria-label="Lessons"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ShowPage, {
            props: { section },
            global: {
                mocks: { route: routeMock },
                stubs,
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
