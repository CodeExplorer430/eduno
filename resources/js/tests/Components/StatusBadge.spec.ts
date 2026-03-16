import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import StatusBadge from '@/Components/StatusBadge.vue';

describe('StatusBadge', () => {
    it('renders with status "submitted"', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'submitted' } });
        expect(wrapper.text()).toContain('submitted');
    });

    it('renders with status "graded"', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'graded' } });
        expect(wrapper.text()).toContain('graded');
    });

    it('renders with status "late"', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'late' } });
        expect(wrapper.text()).toContain('late');
    });

    it('renders with status "pending"', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'pending' } });
        expect(wrapper.text()).toContain('pending');
    });

    it('renders with status "returned"', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'returned' } });
        expect(wrapper.text()).toContain('returned');
    });

    it('applies the correct class for "submitted" status', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'submitted' } });
        expect(wrapper.find('span').classes()).toContain('bg-blue-100');
    });

    it('applies the correct class for "graded" status', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'graded' } });
        expect(wrapper.find('span').classes()).toContain('bg-green-100');
    });

    it('applies the correct class for "late" status', () => {
        const wrapper = mount(StatusBadge, { props: { status: 'late' } });
        expect(wrapper.find('span').classes()).toContain('bg-red-100');
    });
});
