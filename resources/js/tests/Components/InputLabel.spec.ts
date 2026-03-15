import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import InputLabel from '@/Components/InputLabel.vue';

describe('InputLabel', () => {
    it('renders a <label> element', () => {
        const wrapper = mount(InputLabel);
        expect(wrapper.find('label').exists()).toBe(true);
    });

    it('displays the value prop as text content', () => {
        const wrapper = mount(InputLabel, { props: { value: 'Email Address' } });
        expect(wrapper.text()).toContain('Email Address');
    });

    it('renders slot content when no value prop is provided', () => {
        const wrapper = mount(InputLabel, { slots: { default: 'Password' } });
        expect(wrapper.text()).toContain('Password');
    });

    it('prefers value prop over slot content', () => {
        const wrapper = mount(InputLabel, {
            props: { value: 'From Prop' },
            slots: { default: 'From Slot' },
        });
        expect(wrapper.text()).toContain('From Prop');
        expect(wrapper.text()).not.toContain('From Slot');
    });

    it('passes through the for attribute to the <label> element', () => {
        const wrapper = mount(InputLabel, {
            props: { value: 'Email' },
            attrs: { for: 'test-input' },
        });
        expect(wrapper.find('label').attributes('for')).toBe('test-input');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(InputLabel, { props: { value: 'Email' } });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
