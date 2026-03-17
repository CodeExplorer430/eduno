import { describe, it, expect } from 'vitest';
import { axe } from 'vitest-axe';
import Button from 'primevue/button';
import { mountWithPrimeVue } from '../helpers';

describe('PrimeVue Button', () => {
    it('renders a button element', () => {
        const wrapper = mountWithPrimeVue(Button, {
            slots: { default: 'Click me' },
        });
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('is disabled when :disabled prop is set', () => {
        const wrapper = mountWithPrimeVue(Button, {
            props: { disabled: true },
            slots: { default: 'Save' },
        });
        expect(wrapper.find('button').attributes('disabled')).toBeDefined();
    });

    it('passes aria-busy through', () => {
        const wrapper = mountWithPrimeVue(Button, {
            props: { 'aria-busy': true },
            slots: { default: 'Loading' },
        });
        expect(wrapper.find('button').attributes('aria-busy')).toBe('true');
    });

    it('renders severity secondary variant', () => {
        const wrapper = mountWithPrimeVue(Button, {
            props: { severity: 'secondary' },
            slots: { default: 'Cancel' },
        });
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('renders severity danger variant', () => {
        const wrapper = mountWithPrimeVue(Button, {
            props: { severity: 'danger' },
            slots: { default: 'Delete' },
        });
        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(Button, {
            slots: { default: 'Submit' },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
