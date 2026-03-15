import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import NavLink from '@/Components/NavLink.vue';

const LinkStub = {
    template: '<a v-bind="$attrs"><slot /></a>',
    inheritAttrs: true,
};

describe('NavLink', () => {
    it('renders a Link with the provided href', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(wrapper.find('a').attributes('href')).toBe('/dashboard');
    });

    it('renders the slot content', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/courses' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Courses' },
        });
        expect(wrapper.text()).toContain('Courses');
    });

    it('applies active styles when active=true', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: true },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(wrapper.find('a').classes()).toContain('border-indigo-400');
    });

    it('applies inactive styles when active=false', () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard', active: false },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(wrapper.find('a').classes()).toContain('border-transparent');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(NavLink, {
            props: { href: '/dashboard' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Dashboard' },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
