import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const linkStub = {
    template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
    inheritAttrs: false,
};

const globalOpts = { stubs: { Link: linkStub } };

describe('ResponsiveNavLink', () => {
    it('renders slot content as link text', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Dashboard');
    });

    it('applies active classes when active prop is true', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: true },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        const anchor = wrapper.find('a');
        expect(anchor.classes()).toContain('border-blue-400');
        expect(anchor.classes()).toContain('bg-blue-50');
    });

    it('applies inactive classes when active prop is false', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        expect(wrapper.find('a').classes()).toContain('border-transparent');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
