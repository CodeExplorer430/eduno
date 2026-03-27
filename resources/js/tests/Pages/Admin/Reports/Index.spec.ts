import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Admin/Reports/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    StatCard: {
        template: '<div :data-label="label"><slot /></div>',
        props: ['label', 'icon', 'accent', 'animationDelay'],
    },
    Chart: { template: '<canvas />', props: ['type', 'data', 'options'] },
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

const emptyCharts = {
    submission_trend: { labels: [], onTime: [], late: [] },
    grade_distribution: { labels: [], counts: [] },
    late_rate_by_course: { labels: [], rates: [] },
    status_breakdown: { labels: [], counts: [] },
};

const props = {
    stats: { total_submissions: 42, late_submissions: 5, graded: 38, released_grades: 30 },
    charts: emptyCharts,
};

describe('Admin/Reports/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders total_submissions value (42) in the DOM', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props });
        expect(wrapper.text()).toContain('42');
    });

    it('section has aria-labelledby="stats-heading"', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props });
        expect(wrapper.find('section[aria-labelledby="stats-heading"]').exists()).toBe(true);
    });

    it('h2 has id="stats-heading"', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props });
        expect(wrapper.find('h2#stats-heading').exists()).toBe(true);
    });

    it('StatCard for Total Submissions renders label and value 42', () => {
        const wrapper = mount(IndexPage, { global: globalOpts, props });
        const card = wrapper.find('[data-label="Total Submissions"]');
        expect(card.exists()).toBe(true);
        expect(card.text()).toContain('42');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props,
            global: {
                mocks: { route: routeMock },
                stubs,
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
