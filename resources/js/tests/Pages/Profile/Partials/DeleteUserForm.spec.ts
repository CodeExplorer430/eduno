import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {},
        processing: false,
        hasErrors: false,
        delete: vi.fn(),
        clearErrors: vi.fn(),
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
        template: '<button :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy', 'severity'],
    },
    Dialog: {
        template: '<div v-if="visible"><slot /><slot name="footer" /></div>',
        props: ['visible', 'header', 'modal', 'closable'],
    },
};

const globalOpts = { stubs };

describe('Profile/Partials/DeleteUserForm', () => {
    it('renders without crashing', () => {
        const wrapper = mount(DeleteUserForm, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('password input is NOT visible before dialog is opened', () => {
        const wrapper = mount(DeleteUserForm, { global: globalOpts });
        expect(wrapper.find('input#delete-password').exists()).toBe(false);
    });

    it('password input IS visible after trigger button is clicked', async () => {
        const wrapper = mount(DeleteUserForm, { global: globalOpts });
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('input#delete-password').exists()).toBe(true);
    });

    it('password input has aria-describedby="delete-password-error" when dialog is open', async () => {
        const wrapper = mount(DeleteUserForm, { global: globalOpts });
        await wrapper.find('button').trigger('click');
        const input = wrapper.find('input#delete-password');
        expect(input.attributes('aria-describedby')).toBe('delete-password-error');
    });

    it('delete button has aria-busy="false" when not processing', async () => {
        const wrapper = mount(DeleteUserForm, { global: globalOpts });
        await wrapper.find('button').trigger('click');
        const button = wrapper.find('button[aria-busy]');
        expect(button.exists()).toBe(true);
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('passes WCAG axe check with dialog open', async () => {
        const wrapper = mountWithPrimeVue(DeleteUserForm, { global: globalOpts });
        await wrapper.find('button').trigger('click');
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
