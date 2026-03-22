import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import NavLink from '@/Components/NavLink.vue';

const linkStub = {
    template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
    inheritAttrs: false,
};

const globalOpts = { stubs: { Link: linkStub } };

describe('NavLink', () => {
    it('renders slot content as link text', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Dashboard');
    });

    it('applies active border class when active prop is true', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: true },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        expect(wrapper.find('a').classes()).toContain('border-blue-400');
    });

    it('applies transparent border class when active prop is false', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        expect(wrapper.find('a').classes()).toContain('border-transparent');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: false },
            slots: { default: 'Dashboard' },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
