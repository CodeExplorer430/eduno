import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AssignmentCard from '@/Components/AssignmentCard.vue';

const stubs = {
    Link: {
        template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const futureDate = '2099-12-31T23:59:00Z';
const pastDate = '2020-01-01T00:00:00Z';

const baseAssignment = {
    id: 1,
    title: 'Lab Report 1',
    due_at: futureDate,
    max_score: 100,
    course_section_id: 5,
};

describe('AssignmentCard', () => {
    it('renders title in an h2', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('h2').text()).toBe('Lab Report 1');
    });

    it('renders max_score in a dd', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const dds = wrapper.findAll('dd');
        const scoreDD = dds.find((dd) => dd.text().includes('100'));
        expect(scoreDD).toBeDefined();
    });

    it('due date is shown in a time element', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('time').exists()).toBe(true);
    });

    it('time datetime attribute matches the ISO due date string', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('time').attributes('datetime')).toBe(futureDate);
    });

    it('time indicator aria-label contains "Due:" when not past due', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const dd = wrapper.find('dd[aria-label]');
        expect(dd.attributes('aria-label')).toMatch(/^Due:/);
    });

    it('time indicator aria-label contains "Past due:" when due date is in the past', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: { ...baseAssignment, due_at: pastDate } },
            global: globalOpts,
        });
        const dd = wrapper.find('dd[aria-label]');
        expect(dd.attributes('aria-label')).toMatch(/^Past due:/);
    });

    it('view-assignment link has aria-label containing the assignment title', () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const link = wrapper.find('a[aria-label]');
        expect(link.attributes('aria-label')).toContain('Lab Report 1');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(AssignmentCard, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
