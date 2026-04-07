import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AccessibilityPage from '@/Pages/Profile/Accessibility.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: vi.fn(() => ({
        font_size: 'medium',
        high_contrast: false,
        reduced_motion: false,
        simplified_layout: false,
        dark_mode: false,
        language: 'en',
        email_notifications: true,
        patch: vi.fn(),
        processing: false,
        errors: {},
        wasSuccessful: false,
    })),
}));

vi.mock('@/composables/useAppToast', () => ({
    useAppToast: () => ({ success: vi.fn() }),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        InputError: { template: '<span />' },
    },
    mocks: { route: routeMock },
};

const props = {
    preferences: {
        id: 1,
        user_id: 1,
        font_size: 'medium' as const,
        high_contrast: false,
        reduced_motion: false,
        simplified_layout: false,
        dark_mode: false,
        language: 'en',
        email_notifications: true,
    },
};

describe('Profile/Accessibility', () => {
    it('renders without crashing', () => {
        const wrapper = mount(AccessibilityPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('form has font size options', () => {
        const wrapper = mount(AccessibilityPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('accessibility.font_size');
        expect(wrapper.text()).toContain('accessibility.font_size_medium');
    });

    it('form has aria-label', () => {
        const wrapper = mount(AccessibilityPage, { props, global: globalOpts });
        expect(wrapper.find('form[aria-label="Accessibility preferences form"]').exists()).toBe(
            true
        );
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(AccessibilityPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
