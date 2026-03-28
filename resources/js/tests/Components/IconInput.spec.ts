import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IconInput from '@/Components/IconInput.vue';

const IconStub = { template: '<svg />' };

describe('IconInput', () => {
    it('renders an <input> element', () => {
        const wrapper = mount(IconInput, {
            props: { id: 'search', icon: IconStub, modelValue: '' },
        });
        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('input has the correct id', () => {
        const wrapper = mount(IconInput, {
            props: { id: 'search', icon: IconStub, modelValue: '' },
        });
        expect(wrapper.find('input').attributes('id')).toBe('search');
    });

    it('emits update:modelValue when input event fires', async () => {
        const wrapper = mount(IconInput, {
            props: { id: 'search', icon: IconStub, modelValue: '' },
        });
        await wrapper.find('input').setValue('hello');
        expect(wrapper.emitted('update:modelValue')).toBeTruthy();
        expect(wrapper.emitted('update:modelValue')![0]).toEqual(['hello']);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount({
            template: `<div><label for="search">Search</label><IconInput id="search" :icon="icon" modelValue="" /></div>`,
            components: { IconInput },
            data: () => ({ icon: IconStub }),
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
