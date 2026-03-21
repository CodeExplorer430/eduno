import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Lessons/Show.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
};

const lesson = {
    id: 1,
    title: 'Intro Lesson',
    content: 'Welcome!',
    type: 'text',
    published_at: '2026-01-01T00:00:00Z',
    module: {
        title: 'Week 1',
        course_section: {
            id: 1,
            section_name: 'A',
            course: { code: 'CCS123', title: 'Intro to HCI' },
        },
    },
    resources: [],
};

const resourceFixture = {
    id: 99,
    title: 'Lecture Slides.pdf',
    mime_type: 'application/pdf',
    size_bytes: 204800,
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Student/Lessons/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { lesson },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('lesson article has aria-labelledby="lesson-title"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { lesson },
        });
        expect(wrapper.find('article[aria-labelledby="lesson-title"]').exists()).toBe(true);
    });

    it('resources section has aria-labelledby="resources-heading" when resources exist', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { lesson: { ...lesson, resources: [resourceFixture] } },
        });
        expect(wrapper.find('section[aria-labelledby="resources-heading"]').exists()).toBe(true);
    });

    it('download link has aria-label="Download Lecture Slides.pdf"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { lesson: { ...lesson, resources: [resourceFixture] } },
        });
        expect(wrapper.find('a[aria-label="Download Lecture Slides.pdf"]').exists()).toBe(true);
    });

    it('resource list has aria-label="Lesson resources"', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { lesson: { ...lesson, resources: [resourceFixture] } },
        });
        expect(wrapper.find('ul[aria-label="Lesson resources"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ShowPage, {
            props: { lesson },
            global: {
                mocks: { route: routeMock },
                stubs,
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
