import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const linkStub = {
    template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
    inheritAttrs: false,
};

const globalOpts = { stubs: { Link: linkStub } };

describe('GuestLayout', () => {
    it('renders slot content', () => {
        const wrapper = mount(GuestLayout, {
            slots: { default: '<p>Login form</p>' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Login form');
    });

    it('logo link points to home', () => {
        const wrapper = mount(GuestLayout, {
            slots: { default: '<p>Login form</p>' },
            global: globalOpts,
        });
        expect(wrapper.find('a[aria-label]').attributes('href')).toBe('/');
    });

    it('renders the ApplicationLogo component', () => {
        const wrapper = mount(GuestLayout, {
            slots: { default: '<p>Login form</p>' },
            global: globalOpts,
        });
        expect(wrapper.findComponent(ApplicationLogo).exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(GuestLayout, {
            slots: { default: '<p>Login form</p>' },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
