import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import StatusBadge from '@/Components/StatusBadge.vue';

describe('StatusBadge', () => {
    it('applies yellow classes for variant="draft"', () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'draft' } });
        const span = wrapper.find('span');
        expect(span.classes()).toContain('bg-yellow-100');
        expect(span.classes()).toContain('text-yellow-800');
    });

    it('applies red classes for variant="late"', () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'late' } });
        const span = wrapper.find('span');
        expect(span.classes()).toContain('bg-red-100');
        expect(span.classes()).toContain('text-red-700');
    });

    it('applies green classes for variant="published"', () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'published' } });
        const span = wrapper.find('span');
        expect(span.classes()).toContain('bg-green-100');
        expect(span.classes()).toContain('text-green-800');
    });

    it('uses the capitalised variant name as default label', () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'draft' } });
        expect(wrapper.text()).toBe('Draft');
    });

    it('uses a custom label when provided', () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'late', label: 'Overdue' } });
        expect(wrapper.text()).toBe('Overdue');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(StatusBadge, { props: { variant: 'published' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
