import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import DropdownLink from '@/Components/DropdownLink.vue';

const linkStub = {
    template: '<a :href="$attrs.href ?? \'#\'" v-bind="$attrs"><slot /></a>',
    inheritAttrs: false,
};

const globalOpts = { stubs: { Link: linkStub } };

describe('DropdownLink', () => {
    it('renders slot content as link text', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/profile' },
            slots: { default: 'Profile' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Profile');
    });

    it('binds the href prop to the anchor element', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/profile' },
            slots: { default: 'Profile' },
            global: globalOpts,
        });
        expect(wrapper.find('a').attributes('href')).toBe('/profile');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/profile' },
            slots: { default: 'Profile' },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
