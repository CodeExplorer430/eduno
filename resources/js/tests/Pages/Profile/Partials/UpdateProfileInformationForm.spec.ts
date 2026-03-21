import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
        recentlySuccessful: false,
        patch: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
    usePage: vi.fn(() => ({
        props: {
            auth: {
                user: {
                    name: 'Test User',
                    email: 'test@example.com',
                    email_verified_at: null,
                },
            },
        },
    })),
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
    Link: { template: '<a href="#"><slot /></a>' },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Profile/Partials/UpdateProfileInformationForm', () => {
    it('renders without crashing', () => {
        const wrapper = mount(UpdateProfileInformationForm, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('name input has aria-describedby="name-error"', () => {
        const wrapper = mount(UpdateProfileInformationForm, { global: globalOpts });
        const input = wrapper.find('input#name');
        expect(input.attributes('aria-describedby')).toBe('name-error');
    });

    it('email input has aria-describedby="email-error"', () => {
        const wrapper = mount(UpdateProfileInformationForm, { global: globalOpts });
        const input = wrapper.find('input#email');
        expect(input.attributes('aria-describedby')).toBe('email-error');
    });

    it('verification warning shows when mustVerifyEmail=true', () => {
        const wrapper = mount(UpdateProfileInformationForm, {
            props: { mustVerifyEmail: true },
            global: globalOpts,
        });
        expect(wrapper.html()).toContain('unverified');
    });

    it('submit button is disabled when form.processing is true', async () => {
        mockUseForm.mockReturnValueOnce({
            name: 'Test User',
            email: 'test@example.com',
            errors: {},
            processing: true,
            hasErrors: false,
            recentlySuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(UpdateProfileInformationForm, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(UpdateProfileInformationForm, { global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
