import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Instructor/Lessons/Create.vue';
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
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    InputLabel: {
        template: '<label :for="$props.for">{{ value }}<slot /></label>',
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

const section = {
    id: 1,
    section_name: 'A',
    course: { code: 'CCS123', title: 'Intro to HCI' },
};

const module = { id: 10, title: 'Week 1' };

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Lessons/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, {
            global: globalOpts,
            props: { section, module },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('title input has aria-describedby="title-error" when form.errors.title is set', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 0,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, {
            global: globalOpts,
            props: { section, module },
        });
        expect(wrapper.html()).toContain('aria-describedby="title-error"');
    });

    it('type select has aria-describedby="type-error" when form.errors.type is set', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 0,
            published: false,
            errors: { type: 'The type field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, {
            global: globalOpts,
            props: { section, module },
        });
        expect(wrapper.html()).toContain('aria-describedby="type-error"');
    });

    it('shows role="alert" error banner when form.hasErrors is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 0,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, {
            global: globalOpts,
            props: { section, module },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 0,
            published: false,
            errors: {},
            processing: true,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, {
            global: globalOpts,
            props: { section, module },
        });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(CreatePage, {
            props: { section, module },
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    InputLabel: {
                        template: '<label :for="$props.for">{{ value }}<slot /></label>',
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
