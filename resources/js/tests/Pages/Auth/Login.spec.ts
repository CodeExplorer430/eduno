import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Login from '@/Pages/Auth/Login.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        email: '',
        password: '',
        remember: false,
        processing: false,
        errors: {},
        post: vi.fn(),
        reset: vi.fn(),
    }),
}));

vi.mock('@/Layouts/GuestLayout.vue', () => ({
    default: { template: '<div><slot /></div>' },
}));

vi.mock('@/Components/InputError.vue', () => ({
    default: {
        props: ['message', 'class'],
        template: '<p v-if="message" role="alert" class="text-sm text-red-600">{{ message }}</p>',
    },
}));

vi.mock('@/Components/InputLabel.vue', () => ({
    default: {
        props: ['htmlFor', 'value'],
        template: '<label>{{ value }}</label>',
    },
}));

vi.mock('@/Components/TextInput.vue', () => ({
    default: {
        props: ['id', 'type', 'modelValue', 'required', 'autofocus', 'autocomplete', 'class'],
        template: '<input :id="id" :type="type" :value="modelValue" />',
    },
}));

vi.mock('@/Components/PrimaryButton.vue', () => ({
    default: { template: '<button type="submit"><slot /></button>' },
}));

vi.mock('@/Components/Checkbox.vue', () => ({
    default: {
        props: ['checked', 'name'],
        template: '<input type="checkbox" :name="name" />',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string): string => `/${name}`;
});

describe('Login', () => {
    it('renders email and password fields', () => {
        const wrapper = mount(Login, {
            props: { canResetPassword: true, status: undefined },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        expect(wrapper.find('input[type="email"]').exists()).toBe(true);
        expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    });

    it('renders status message with role=status when status prop provided', () => {
        const wrapper = mount(Login, {
            props: { canResetPassword: false, status: 'Password reset email sent.' },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
        });
        const statusEl = wrapper.find('[role="status"]');
        expect(statusEl.exists()).toBe(true);
        expect(statusEl.text()).toContain('Password reset email sent.');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(Login, {
            props: { canResetPassword: true, status: undefined },
            global: {
                mocks: { route: (name: string) => `/${name}` },
            },
            attachTo: document.body,
        });
        const results = await axe(wrapper.element as Element);
        expect(results.violations).toHaveLength(0);
    });
});
