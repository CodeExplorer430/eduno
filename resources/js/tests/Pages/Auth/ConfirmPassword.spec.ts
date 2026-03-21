import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ConfirmPasswordPage from '@/Pages/Auth/ConfirmPassword.vue';
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

describe('Auth/ConfirmPassword', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ConfirmPasswordPage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(ConfirmPasswordPage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button has aria-busy="true" when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            password: '',
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(ConfirmPasswordPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('aria-busy')).toBe('true');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            password: '',
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(ConfirmPasswordPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('shows "Confirming" text in button when processing', () => {
        mockUseForm.mockReturnValueOnce({
            password: '',
            errors: {},
            processing: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(ConfirmPasswordPage, { global: globalOpts });
        expect(wrapper.find('button[type="submit"]').text()).toContain('Confirming');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ConfirmPasswordPage, {
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
