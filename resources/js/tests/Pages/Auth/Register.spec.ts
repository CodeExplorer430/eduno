import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import RegisterPage from '@/Pages/Auth/Register.vue';
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
    PrimaryButton: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Auth/Register', () => {
    it('renders without crashing', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('name input has aria-describedby="name-error"', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="name-error"');
    });

    it('email input has aria-describedby="email-error"', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="email-error"');
    });

    it('password input has aria-describedby="password-error"', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="password-error"');
    });

    it('password confirmation input has aria-describedby="password_confirmation-error"', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="password_confirmation-error"');
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(RegisterPage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: {},
            processing: true,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(RegisterPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(RegisterPage, {
            global: {
                mocks: { route: routeMock },
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
