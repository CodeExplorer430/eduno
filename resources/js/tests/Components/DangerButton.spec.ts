import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import DangerButton from '@/Components/DangerButton.vue';

describe('DangerButton', () => {
    it('renders a <button> element', () => {
        const wrapper = mount(DangerButton);
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('renders slot content inside the button', () => {
        const wrapper = mount(DangerButton, { slots: { default: 'Delete Account' } });
        expect(wrapper.find('button').text()).toBe('Delete Account');
    });

    it('applies destructive styling (red background)', () => {
        const wrapper = mount(DangerButton);
        expect(wrapper.find('button').classes()).toContain('bg-red-600');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(DangerButton, { slots: { default: 'Delete' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
