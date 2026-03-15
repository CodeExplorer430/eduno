import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Checkbox from '@/Components/Checkbox.vue';

describe('Checkbox', () => {
    it('renders an <input type="checkbox">', () => {
        const wrapper = mount(Checkbox, { props: { checked: false } });
        expect(wrapper.find('input[type="checkbox"]').exists()).toBe(true);
    });

    it('reflects the checked prop', () => {
        const wrapper = mount(Checkbox, { props: { checked: true } });
        expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(true);
    });

    it('passes the value prop to the input element', () => {
        const wrapper = mount(Checkbox, { props: { checked: false, value: 'option-a' } });
        expect(wrapper.find('input').attributes('value')).toBe('option-a');
    });

    it('emits update:checked when the checkbox changes', async () => {
        const wrapper = mount(Checkbox, { props: { checked: false } });
        await wrapper.find('input').setValue(true);
        expect(wrapper.emitted('update:checked')).toBeTruthy();
    });

    it('has no axe violations', async () => {
        const wrapper = mount({
            template: `<div><label for="chk">Accept terms</label><Checkbox id="chk" :checked="false" /></div>`,
            components: { Checkbox },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
