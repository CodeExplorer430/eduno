import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import VerifyEmailPage from '@/Pages/Auth/VerifyEmail.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (): Record<string, unknown> => ({
        errors: {} as Record<string, string>,
        processing: false,
        post: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: () => mockUseForm(),
}));

vi.stubGlobal(
    'route',
    vi.fn(() => '/')
);

const routeMock = vi.fn(() => '/');

const stubs = {
    Head: true,
    GuestLayout: { template: '<div><slot /></div>' },
    Link: { template: '<a href="#"><slot /></a>' },
    PrimaryButton: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Auth/VerifyEmail', () => {
    it('renders without crashing', () => {
        const wrapper = mount(VerifyEmailPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(VerifyEmailPage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(VerifyEmailPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('shows verification-link-sent message when status === "verification-link-sent"', () => {
        const wrapper = mount(VerifyEmailPage, {
            props: { status: 'verification-link-sent' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('A new verification link has been sent');
    });

    it('does not show verification-link-sent message when status is undefined', () => {
        const wrapper = mount(VerifyEmailPage, { global: globalOpts });
        expect(wrapper.text()).not.toContain('A new verification link has been sent');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(VerifyEmailPage, {
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    Button: {
                        template: '<button type="submit"><slot /></button>',
                    },
                },
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
