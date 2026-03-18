import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import WelcomePage from '@/Pages/Welcome.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const stubs = {
    Head: true,
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
};

const routeMock = vi.fn(() => '/');

const globalUnauthenticated = {
    stubs,
    mocks: { route: routeMock, $page: { props: { auth: { user: null } } } },
};

const globalAuthenticated = {
    stubs,
    mocks: {
        route: routeMock,
        $page: { props: { auth: { user: { id: 1, name: 'Test' } } } },
    },
};

const baseProps = {
    canLogin: true,
    canRegister: true,
    laravelVersion: '12.0.0',
    phpVersion: '8.4.0',
};

describe('Welcome', () => {
    it('renders without crashing', () => {
        const wrapper = mount(WelcomePage, { props: baseProps, global: globalUnauthenticated });
        expect(wrapper.exists()).toBe(true);
    });

    it('footer shows Laravel version string', () => {
        const wrapper = mount(WelcomePage, { props: baseProps, global: globalUnauthenticated });
        expect(wrapper.find('footer').text()).toContain('12.0.0');
    });

    it('login link renders when unauthenticated and canLogin=true', () => {
        const wrapper = mount(WelcomePage, { props: baseProps, global: globalUnauthenticated });
        expect(wrapper.html()).toContain('Log in');
    });

    it('register link renders when unauthenticated and canRegister=true', () => {
        const wrapper = mount(WelcomePage, { props: baseProps, global: globalUnauthenticated });
        expect(wrapper.html()).toContain('Register');
    });

    it('dashboard link renders when user is authenticated', () => {
        const wrapper = mount(WelcomePage, { props: baseProps, global: globalAuthenticated });
        expect(wrapper.html()).toContain('Dashboard');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(WelcomePage, {
            props: baseProps,
            global: globalUnauthenticated,
        });
        const results = await axe(wrapper.element, {
            rules: { region: { enabled: false }, 'image-alt': { enabled: false } },
        });
        expect(results).toHaveNoViolations();
    });
});
