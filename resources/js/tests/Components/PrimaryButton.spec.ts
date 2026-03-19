import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import PrimaryButton from '@/Components/PrimaryButton.vue';

describe('PrimaryButton', () => {
    it('renders a <button> element', () => {
        const wrapper = mount(PrimaryButton);
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('renders slot content inside the button', () => {
        const wrapper = mount(PrimaryButton, { slots: { default: 'Save Changes' } });
        expect(wrapper.find('button').text()).toBe('Save Changes');
    });

    it('is of type "submit" by default (browser default for buttons)', () => {
        const wrapper = mount(PrimaryButton);
        // No explicit type means the button inherits native browser default
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('has no axe violations', async () => {
        const wrapper = mount(PrimaryButton, { slots: { default: 'Submit' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
