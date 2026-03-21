import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ForgotPasswordPage from '@/Pages/Auth/ForgotPassword.vue';
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
    Button: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const globalOpts = { stubs };

describe('Auth/ForgotPassword', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ForgotPasswordPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('email input has aria-describedby="email-error"', () => {
        const wrapper = mount(ForgotPasswordPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="email-error"');
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(ForgotPasswordPage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            email: '',
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(ForgotPasswordPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('status message is rendered when status prop is set', () => {
        const wrapper = mount(ForgotPasswordPage, {
            props: { status: 'A password reset link has been sent.' },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('A password reset link has been sent.');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ForgotPasswordPage, {
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
