import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import LoginPage from '@/Pages/Auth/Login.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
        post: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

vi.stubGlobal(
    'route',
    vi.fn(() => '/')
);

const stubs = {
    Head: true,
    GuestLayout: { template: '<div><slot /></div>' },
    Link: { template: '<a href="#"><slot /></a>' },
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
    Checkbox: { template: '<input type="checkbox" />' },
    PrimeCheckbox: { template: '<input type="checkbox" />' },
    PrimaryButton: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const globalOpts = { stubs };

describe('Auth/Login', () => {
    it('renders without crashing', () => {
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('email input has aria-describedby="email-error"', () => {
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="email-error"');
    });

    it('password input has aria-describedby="password-error"', () => {
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="password-error"');
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(LoginPage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            email: '',
            password: '',
            remember: false,
            errors: {},
            processing: true,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('shows role="alert" banner when form.hasErrors is true and no field errors', () => {
        mockUseForm.mockReturnValueOnce({
            email: '',
            password: '',
            remember: false,
            errors: {},
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('email input has aria-invalid="true" when email error is present', () => {
        mockUseForm.mockReturnValueOnce({
            email: '',
            password: '',
            remember: false,
            errors: { email: 'These credentials do not match our records.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(LoginPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-invalid="true"');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(LoginPage, {
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
