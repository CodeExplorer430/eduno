import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Profile/Edit.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a href="#" v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a href="#" v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    UpdateProfileInformationForm: { template: '<div />' },
    UpdatePasswordForm: { template: '<div />' },
    DeleteUserForm: { template: '<div />' },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Profile/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('header h2 contains "Profile"', () => {
        const wrapper = mount(EditPage, { global: globalOpts });
        const headings = wrapper.findAll('h2');
        const profileHeading = headings.find((h) => h.text().includes('Profile'));
        expect(profileHeading).toBeDefined();
    });

    it('accessibility preferences section heading exists', () => {
        const wrapper = mount(EditPage, { global: globalOpts });
        const headings = wrapper.findAll('h2');
        const accessibilityHeading = headings.find((h) =>
            h.text().includes('Accessibility Preferences')
        );
        expect(accessibilityHeading).toBeDefined();
    });

    it('"Manage Accessibility Settings" link is present', () => {
        const wrapper = mount(EditPage, { global: globalOpts });
        expect(wrapper.html()).toContain('Manage Accessibility Settings');
    });

    it('renders without crashing when mustVerifyEmail=true', () => {
        const wrapper = mount(EditPage, {
            props: { mustVerifyEmail: true },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, { global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
