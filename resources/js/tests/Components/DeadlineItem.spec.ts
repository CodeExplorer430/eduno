import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import DeadlineItem from '@/Components/DeadlineItem.vue';

const baseAssignment = {
    id: 1,
    title: 'Lab Report 1',
    course_name: 'Human-Computer Interaction',
    course_code: 'CCS123',
    due_at: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000).toISOString(), // 5 days from now
};

describe('DeadlineItem', () => {
    it('renders assignment title', () => {
        const wrapper = mount(DeadlineItem, {
            props: { assignment: baseAssignment },
        });
        expect(wrapper.text()).toContain('Lab Report 1');
    });

    it('shows red urgency class when due in less than 24 hours', () => {
        const soonAssignment = {
            ...baseAssignment,
            due_at: new Date(Date.now() + 12 * 60 * 60 * 1000).toISOString(),
        };
        const wrapper = mount(DeadlineItem, {
            props: { assignment: soonAssignment },
        });
        expect(wrapper.find('li').classes()).toContain('border-red-400');
    });

    it('shows amber urgency class when due in 48 hours', () => {
        const soonAssignment = {
            ...baseAssignment,
            due_at: new Date(Date.now() + 48 * 60 * 60 * 1000).toISOString(),
        };
        const wrapper = mount(DeadlineItem, {
            props: { assignment: soonAssignment },
        });
        expect(wrapper.find('li').classes()).toContain('border-amber-400');
    });

    it('shows green urgency class when due in 5 days', () => {
        const wrapper = mount(DeadlineItem, {
            props: { assignment: baseAssignment },
        });
        expect(wrapper.find('li').classes()).toContain('border-green-400');
    });

    it('<li> has aria-label containing title and course', () => {
        const wrapper = mount(DeadlineItem, {
            props: { assignment: baseAssignment },
        });
        const ariaLabel = wrapper.find('li').attributes('aria-label') ?? '';
        expect(ariaLabel).toContain('Lab Report 1');
        expect(ariaLabel).toContain('Human-Computer Interaction');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount({
            template: `<ul><DeadlineItem :assignment="assignment" /></ul>`,
            components: { DeadlineItem },
            data: () => ({ assignment: baseAssignment }),
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
