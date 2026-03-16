import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import TextInput from '@/Components/TextInput.vue';

describe('TextInput', () => {
    it('renders an input element', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
        });
        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('sets aria-describedby when ariaDescribedby prop is passed', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
                ariaDescribedby: 'test-error',
            },
        });
        expect(wrapper.find('input').attributes('aria-describedby')).toBe('test-error');
    });

    it('does not set aria-describedby when prop is not passed', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
        });
        expect(wrapper.find('input').attributes('aria-describedby')).toBeUndefined();
    });
});
