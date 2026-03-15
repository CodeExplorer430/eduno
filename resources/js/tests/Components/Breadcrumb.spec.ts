import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const LinkStub = {
    template: '<a v-bind="$attrs"><slot /></a>',
    inheritAttrs: true,
};

const crumbs = [
    { label: 'Courses', href: '/courses' },
    { label: 'CCS 101', href: '/courses/1' },
    { label: 'Module 1' },
];

describe('Breadcrumb', () => {
    it('renders a <nav> with aria-label="Breadcrumb"', () => {
        const wrapper = mount(Breadcrumb, {
            props: { crumbs },
            global: { stubs: { Link: LinkStub } },
        });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('marks the last crumb with aria-current="page"', () => {
        const wrapper = mount(Breadcrumb, {
            props: { crumbs },
            global: { stubs: { Link: LinkStub } },
        });
        const last = wrapper.find('[aria-current="page"]');
        expect(last.exists()).toBe(true);
        expect(last.text()).toBe('Module 1');
    });

    it('renders links for non-last crumbs', () => {
        const wrapper = mount(Breadcrumb, {
            props: { crumbs },
            global: { stubs: { Link: LinkStub } },
        });
        const links = wrapper.findAll('a');
        expect(links).toHaveLength(2);
        expect(links[0].attributes('href')).toBe('/courses');
        expect(links[1].attributes('href')).toBe('/courses/1');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Breadcrumb, {
            props: { crumbs },
            global: { stubs: { Link: LinkStub } },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
