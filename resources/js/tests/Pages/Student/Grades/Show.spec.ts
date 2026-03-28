import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Grades/Show.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
    },
    mocks: { route: routeMock },
};

const props = {
    grade: {
        id: 1,
        score: 92,
        feedback: 'Excellent',
        released_at: '2026-01-01T00:00:00Z',
        assignment: {
            id: 1,
            title: 'Midterm',
            max_score: 100,
            course_section_id: 1,
        },
    },
};

describe('Student/Grades/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows score value', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('92');
    });

    it('breadcrumb has aria-label="Breadcrumb"', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('shows assignment title', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Midterm');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
