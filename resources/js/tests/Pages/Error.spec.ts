import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ErrorPage from '@/Pages/Error.vue';

vi.mock('@inertiajs/vue3', () => ({
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const globalOpts = {
    stubs: {
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
    },
};

describe('Error', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ErrorPage, { props: { status: 404 }, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows Page Not Found title for 404', () => {
        const wrapper = mount(ErrorPage, { props: { status: 404 }, global: globalOpts });
        expect(wrapper.find('h1').text()).toContain('Page Not Found');
    });

    it('shows Access Denied title for 403', () => {
        const wrapper = mount(ErrorPage, { props: { status: 403 }, global: globalOpts });
        expect(wrapper.find('h1').text()).toContain('Access Denied');
    });

    it('shows Server Error title for 500', () => {
        const wrapper = mount(ErrorPage, { props: { status: 500 }, global: globalOpts });
        expect(wrapper.find('h1').text()).toContain('Server Error');
    });

    it('status number has aria-hidden="true"', () => {
        const wrapper = mount(ErrorPage, { props: { status: 404 }, global: globalOpts });
        const statusEl = wrapper.find('[aria-hidden="true"]');
        expect(statusEl.exists()).toBe(true);
        expect(statusEl.text()).toBe('404');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(ErrorPage, { props: { status: 404 }, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
