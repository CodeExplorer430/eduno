import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Admin/Users/Edit.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
        wasSuccessful: false,
        patch: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>', inheritAttrs: false },
    InputLabel: {
        template: '<label :for="$props.for"><slot /></label>',
        props: ['for', 'value'],
    },
    InputError: {
        template: '<span :id="$attrs.id" />',
        inheritAttrs: false,
    },
    Button: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const userFixture = { id: 1, name: 'Alice', email: 'alice@example.com', role: 'student' };
const rolesFixture = [
    { name: 'Student', value: 'student' },
    { name: 'Instructor', value: 'instructor' },
    { name: 'Admin', value: 'admin' },
];

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Admin/Users/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { user: userFixture, roles: rolesFixture },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('role select has aria-describedby="user-role-error" (unconditional)', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { user: userFixture, roles: rolesFixture },
        });
        const select = wrapper.find('select#user-role');
        expect(select.attributes('aria-describedby')).toBe('user-role-error');
    });

    it('aria-invalid="true" on role select when form.errors.role is set', () => {
        mockUseForm.mockReturnValueOnce({
            role: 'student',
            errors: { role: 'The role field is required.' },
            processing: false,
            hasErrors: true,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { user: userFixture, roles: rolesFixture },
        });
        const select = wrapper.find('select#user-role');
        expect(select.attributes('aria-invalid')).toBe('true');
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { user: userFixture, roles: rolesFixture },
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            role: 'student',
            errors: {},
            processing: true,
            hasErrors: false,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { user: userFixture, roles: rolesFixture },
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, {
            props: { user: userFixture, roles: rolesFixture },
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
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
