import { describe, it, expect } from 'vitest';
import { axe } from 'vitest-axe';
import InputText from 'primevue/inputtext';
import { mountWithPrimeVue } from '../helpers';

describe('PrimeVue InputText', () => {
    it('renders an input element', () => {
        const wrapper = mountWithPrimeVue(InputText, {
            props: { modelValue: '' },
        });
        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('passes aria-describedby through', () => {
        const wrapper = mountWithPrimeVue(InputText, {
            props: { modelValue: '', 'aria-describedby': 'field-error' },
        });
        expect(wrapper.find('input').attributes('aria-describedby')).toBe('field-error');
    });

    it('passes aria-invalid through', () => {
        const wrapper = mountWithPrimeVue(InputText, {
            props: { modelValue: '', 'aria-invalid': 'true' },
        });
        expect(wrapper.find('input').attributes('aria-invalid')).toBe('true');
    });

    it('passes type attribute through', () => {
        const wrapper = mountWithPrimeVue(InputText, {
            props: { modelValue: '', type: 'email' },
        });
        expect(wrapper.find('input').attributes('type')).toBe('email');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(InputText, {
            props: { modelValue: '', id: 'test-input', 'aria-label': 'Test field' },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
