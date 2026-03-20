import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import TextInput from '@/Components/TextInput.vue';

describe('TextInput', () => {
    it('renders an <input> element', () => {
        const wrapper = mount(TextInput, { props: { modelValue: '' } });
        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('reflects the modelValue prop', () => {
        const wrapper = mount(TextInput, { props: { modelValue: 'hello' } });
        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('hello');
    });

    it('exposes a focus() method', () => {
        const wrapper = mount(TextInput, { props: { modelValue: '' } });
        expect(typeof (wrapper.vm as { focus: () => void }).focus).toBe('function');
    });

    it('calls focus on the input when focus() is invoked', async () => {
        const wrapper = mount(TextInput, { props: { modelValue: '' } });
        const focusSpy = vi.spyOn(wrapper.find('input').element as HTMLInputElement, 'focus');
        (wrapper.vm as { focus: () => void }).focus();
        expect(focusSpy).toHaveBeenCalled();
    });

    it('forwards aria-describedby to the input element', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
            attrs: { 'aria-describedby': 'hint-text' },
        });
        expect(wrapper.find('input').attributes('aria-describedby')).toBe('hint-text');
    });

    it('forwards aria-invalid to the input element', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
            attrs: { 'aria-invalid': 'true' },
        });
        expect(wrapper.find('input').attributes('aria-invalid')).toBe('true');
    });

    it('has no axe violations', async () => {
        const wrapper = mount({
            template: `<div><label for="txt">Name</label><TextInput id="txt" v-model="val" /></div>`,
            components: { TextInput },
            data: () => ({ val: '' }),
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
