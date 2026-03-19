import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SecondaryButton from '@/Components/SecondaryButton.vue';

describe('SecondaryButton', () => {
    it('renders a <button> element', () => {
        const wrapper = mount(SecondaryButton);
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('defaults to type="button"', () => {
        const wrapper = mount(SecondaryButton);
        expect(wrapper.find('button').attributes('type')).toBe('button');
    });

    it('binds the type prop to the button', () => {
        const wrapper = mount(SecondaryButton, { props: { type: 'submit' } });
        expect(wrapper.find('button').attributes('type')).toBe('submit');
    });

    it('renders slot content', () => {
        const wrapper = mount(SecondaryButton, { slots: { default: 'Cancel' } });
        expect(wrapper.find('button').text()).toBe('Cancel');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(SecondaryButton, { slots: { default: 'Cancel' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
