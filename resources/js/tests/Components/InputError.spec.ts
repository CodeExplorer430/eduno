import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import InputError from '@/Components/InputError.vue';

describe('InputError', () => {
    it('displays the error message when provided', () => {
        const wrapper = mount(InputError, { props: { message: 'This field is required.' } });
        expect(wrapper.text()).toContain('This field is required.');
    });

    it('is visually hidden when no message is provided', () => {
        const wrapper = mount(InputError, { props: { message: '' } });
        // v-show sets display:none via inline style when the condition is falsy
        expect(wrapper.find('div').element.style.display).toBe('none');
    });

    it('becomes visible when a message is supplied', async () => {
        const wrapper = mount(InputError, { props: { message: 'Invalid value.' } });
        expect(wrapper.find('div').isVisible()).toBe(true);
    });

    it('renders the message inside a <p> element', () => {
        const wrapper = mount(InputError, { props: { message: 'Error text.' } });
        expect(wrapper.find('p').exists()).toBe(true);
        expect(wrapper.find('p').text()).toBe('Error text.');
    });

    it('has role="alert" on the <p> element', () => {
        const wrapper = mount(InputError, { props: { message: 'Required field.' } });
        expect(wrapper.find('p').attributes('role')).toBe('alert');
    });

    it('has no axe violations when displaying an error', async () => {
        const wrapper = mount(InputError, { props: { message: 'Invalid email address.' } });
        // Disable the region rule: components are tested in isolation, not as full pages
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
