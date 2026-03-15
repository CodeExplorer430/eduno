import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Pagination from '@/Components/Pagination.vue';

const LinkStub = {
    template: '<a v-bind="$attrs"><slot /></a>',
    inheritAttrs: true,
};

const sampleLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '/items?page=1', label: '1', active: true },
    { url: '/items?page=2', label: '2', active: false },
    { url: '/items?page=2', label: 'Next &raquo;', active: false },
];

describe('Pagination', () => {
    it('renders a <nav> with aria-label="Pagination"', () => {
        const wrapper = mount(Pagination, {
            props: { links: sampleLinks },
            global: { stubs: { Link: LinkStub } },
        });
        expect(wrapper.find('nav[aria-label="Pagination"]').exists()).toBe(true);
    });

    it('marks the active link with aria-current="page"', () => {
        const wrapper = mount(Pagination, {
            props: { links: sampleLinks },
            global: { stubs: { Link: LinkStub } },
        });
        expect(wrapper.find('[aria-current="page"]').exists()).toBe(true);
    });

    it('renders disabled (null URL) items as <span> elements', () => {
        const wrapper = mount(Pagination, {
            props: { links: sampleLinks },
            global: { stubs: { Link: LinkStub } },
        });
        expect(wrapper.find('span').exists()).toBe(true);
    });

    it('renders navigable links as <Link> components', () => {
        const wrapper = mount(Pagination, {
            props: { links: sampleLinks },
            global: { stubs: { Link: LinkStub } },
        });
        // Three links have URLs so three <a> stubs should appear
        expect(wrapper.findAll('a').length).toBe(3);
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Pagination, {
            props: { links: sampleLinks },
            global: { stubs: { Link: LinkStub } },
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
