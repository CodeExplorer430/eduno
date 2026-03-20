import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Register from '@/Pages/Auth/Register.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: (initial: Record<string, unknown>) => ({
        ...initial,
        processing: false,
        errors: {},
        post: vi.fn(),
        reset: vi.fn(),
    }),
}));

vi.mock('@/Layouts/GuestLayout.vue', () => ({
    default: {
        template: '<main><slot /></main>',
    },
}));

vi.mock('@/Components/InputLabel.vue', () => ({
    default: {
        props: ['value'],
        template: '<label>{{ value }}</label>',
    },
}));

vi.mock('@/Components/TextInput.vue', () => ({
    default: {
        props: ['id', 'type', 'modelValue', 'required', 'autofocus', 'autocomplete'],
        emits: ['update:modelValue'],
        template:
            '<input :id="id" :type="type || \'text\'" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
    },
}));

vi.mock('@/Components/InputError.vue', () => ({
    default: {
        props: ['message'],
        template: '<p v-if="message" role="alert">{{ message }}</p>',
    },
}));

vi.mock('@/Components/PrimaryButton.vue', () => ({
    default: {
        template: '<button type="submit"><slot /></button>',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

describe('Auth/Register', () => {
    it('renders the Name field with label', () => {
        const wrapper = mount(Register, {
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Name');
    });

    it('renders the Email field with label', () => {
        const wrapper = mount(Register, {
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Email');
    });

    it('renders the Password field with label', () => {
        const wrapper = mount(Register, {
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Password');
    });

    it('renders the Confirm Password field with label', () => {
        const wrapper = mount(Register, {
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Confirm Password');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Register, {
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
