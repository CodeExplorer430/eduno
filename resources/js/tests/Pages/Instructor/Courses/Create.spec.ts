import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Instructor/Courses/Create.vue';
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

const routeMock = vi.fn(() => '/');

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

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Courses/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('course code input has aria-describedby="course-code-error" (unconditional)', () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        expect(wrapper.html()).toContain('aria-describedby="course-code-error"');
    });

    it('aria-invalid="true" on code input when form.errors.code is set', () => {
        mockUseForm.mockReturnValueOnce({
            code: '',
            title: '',
            description: '',
            department: '',
            term: '',
            academic_year: '',
            errors: { code: 'The code field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, { global: globalOpts });
        const input = wrapper.find('input#course-code');
        expect(input.attributes('aria-invalid')).toBe('true');
    });

    it('shows role="alert" error banner when form.hasErrors is true and errors exist', () => {
        mockUseForm.mockReturnValueOnce({
            code: '',
            title: '',
            description: '',
            department: '',
            term: '',
            academic_year: '',
            errors: { code: 'The code field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, { global: globalOpts });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(CreatePage, { global: globalOpts });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(CreatePage, {
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    InputLabel: {
                        template: '<label :for="$props.for"><slot /></label>',
                        props: ['for', 'value'],
                    },
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
