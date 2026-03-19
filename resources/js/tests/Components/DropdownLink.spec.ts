import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import DropdownLink from '@/Components/DropdownLink.vue';

const LinkStub = {
    template: '<a v-bind="$attrs"><slot /></a>',
    inheritAttrs: true,
};

describe('DropdownLink', () => {
    it('renders a Link element with the correct href', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/courses' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Courses' },
        });
        expect(wrapper.find('a').attributes('href')).toBe('/courses');
    });

    it('renders slot content', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/courses' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'My Courses' },
        });
        expect(wrapper.text()).toContain('My Courses');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/courses' },
            global: { stubs: { Link: LinkStub } },
            slots: { default: 'Courses' },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
