import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

describe('ApplicationLogo', () => {
    it('renders an svg element', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('svg').exists()).toBe(true);
    });

    it('has the correct viewBox attribute', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('svg').attributes('viewBox')).toBe('0 0 316 316');
    });

    it('contains a path element', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('path').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(ApplicationLogo);
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
