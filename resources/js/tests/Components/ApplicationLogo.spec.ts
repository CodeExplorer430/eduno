import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

describe('ApplicationLogo', () => {
    it('renders an <svg> element', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('svg').exists()).toBe(true);
    });

    it('has aria-hidden="true" on the SVG (decorative image)', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('svg').attributes('aria-hidden')).toBe('true');
    });

    it('has focusable="false" on the SVG', () => {
        const wrapper = mount(ApplicationLogo);
        expect(wrapper.find('svg').attributes('focusable')).toBe('false');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(ApplicationLogo);
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
