import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import InputError from '@/Components/InputError.vue';

describe('InputError', () => {
    it('renders message text when message prop is provided', () => {
        const wrapper = mount(InputError, {
            props: { message: 'This field is required.' },
        });
        expect(wrapper.text()).toContain('This field is required.');
    });

    it('sets id attribute from id prop', () => {
        const wrapper = mount(InputError, {
            props: { message: 'Error', id: 'grade-score-error' },
        });
        expect(wrapper.find('div').attributes('id')).toBe('grade-score-error');
    });

    it('hides the container via v-show when no message prop is given', () => {
        const wrapper = mount(InputError, {
            attachTo: document.body,
        });
        const div = wrapper.find('div');
        expect(div.exists()).toBe(true);
        expect(div.isVisible()).toBe(false);
        wrapper.unmount();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(InputError, {
            props: { message: 'This field is required.', id: 'test-error' },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
