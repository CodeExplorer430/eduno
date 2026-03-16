import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import InputLabel from '@/Components/InputLabel.vue';

describe('InputLabel', () => {
    it('renders a label element', () => {
        const wrapper = mount(InputLabel, {
            slots: { default: 'My Label' },
        });
        expect(wrapper.find('label').exists()).toBe(true);
    });

    it('sets the for attribute when the for prop is passed', () => {
        const wrapper = mount(InputLabel, {
            props: { for: 'test-id' },
            slots: { default: 'My Label' },
        });
        expect(wrapper.find('label').attributes('for')).toBe('test-id');
    });

    it('renders slot content inside the label', () => {
        const wrapper = mount(InputLabel, {
            slots: { default: 'Username' },
        });
        expect(wrapper.text()).toContain('Username');
    });

    it('renders the value prop when provided', () => {
        const wrapper = mount(InputLabel, {
            props: { value: 'Email Address' },
        });
        expect(wrapper.text()).toContain('Email Address');
    });
});
