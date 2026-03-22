import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ResetPasswordPage from '@/Pages/Auth/ResetPassword.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        post: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

vi.stubGlobal(
    'route',
    vi.fn(() => '/')
);

const props = { email: 'test@example.com', token: 'abc123' };

const stubs = {
    Head: true,
    GuestLayout: { template: '<div><slot /></div>' },
    InputLabel: {
        template: '<label :for="$props.for">{{ value }}</label>',
        props: ['for', 'value'],
    },
    InputError: {
        template: '<span :id="$attrs.id" />',
        inheritAttrs: false,
    },
    InputText: {
        template: '<input v-bind="$attrs" />',
        inheritAttrs: false,
    },
    PrimaryButton: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const globalOpts = { stubs };

describe('Auth/ResetPassword', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ResetPasswordPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('email input has aria-describedby="email-error" (unconditional)', () => {
        const wrapper = mount(ResetPasswordPage, { props, global: globalOpts });
        const input = wrapper.find('input[type="email"]');
        expect(input.attributes('aria-describedby')).toBe('email-error');
    });

    it('password input has aria-describedby="password-error" (unconditional)', () => {
        const wrapper = mount(ResetPasswordPage, { props, global: globalOpts });
        const passwordInputs = wrapper.findAll('input[type="password"]');
        const passwordInput = passwordInputs[0];
        expect(passwordInput.attributes('aria-describedby')).toBe('password-error');
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(ResetPasswordPage, { props, global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            token: props.token,
            email: props.email,
            password: '',
            password_confirmation: '',
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(ResetPasswordPage, { props, global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ResetPasswordPage, {
            props,
            global: {
                stubs: {
                    ...stubs,
                    InputText: { template: '<input v-bind="$attrs" />', inheritAttrs: false },
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
