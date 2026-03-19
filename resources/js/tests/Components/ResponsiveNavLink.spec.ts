import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const LinkStub = {
    template: '<a v-bind="$attrs"><slot /></a>',
    inheritAttrs: true,
};

describe('ResponsiveNavLink', () => {
    it('applies active classes when active=true', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: true },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(wrapper.find('a').classes()).toContain('border-indigo-400');
    });

    it('applies inactive classes when active=false', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard', active: false },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(wrapper.find('a').classes()).toContain('border-transparent');
    });

    it('renders slot content', () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/courses' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Courses' },
        });
        expect(wrapper.text()).toContain('Courses');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: '/dashboard' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
