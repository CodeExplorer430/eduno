import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
        recentlySuccessful: false,
        put: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

const stubs = {
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
        template: '<button type="submit" :disabled="disabled"><slot /></button>',
        props: ['disabled'],
    },
};

const globalOpts = { stubs };

describe('Profile/Partials/UpdatePasswordForm', () => {
    it('renders without crashing', () => {
        const wrapper = mount(UpdatePasswordForm, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('current password input has aria-describedby="current_password-error"', () => {
        const wrapper = mount(UpdatePasswordForm, { global: globalOpts });
        const input = wrapper.find('input#current_password');
        expect(input.attributes('aria-describedby')).toBe('current_password-error');
    });

    it('new password input has aria-describedby="password-error"', () => {
        const wrapper = mount(UpdatePasswordForm, { global: globalOpts });
        const input = wrapper.find('input#password');
        expect(input.attributes('aria-describedby')).toBe('password-error');
    });

    it('confirm password input has aria-describedby="password_confirmation-error"', () => {
        const wrapper = mount(UpdatePasswordForm, { global: globalOpts });
        const input = wrapper.find('input#password_confirmation');
        expect(input.attributes('aria-describedby')).toBe('password_confirmation-error');
    });

    it('submit button is disabled when form.processing is true', async () => {
        mockUseForm.mockReturnValueOnce({
            current_password: '',
            password: '',
            password_confirmation: '',
            errors: {},
            processing: true,
            hasErrors: false,
            recentlySuccessful: false,
            put: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(UpdatePasswordForm, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(UpdatePasswordForm, { global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
