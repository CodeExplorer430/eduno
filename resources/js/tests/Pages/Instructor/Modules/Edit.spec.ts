import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Instructor/Modules/Edit.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
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

const moduleFixture = {
    id: 10,
    title: 'Week 1',
    description: null,
    order_no: 1,
    published_at: null,
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Modules/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, module: moduleFixture },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('"Edit Module" text is in h1', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, module: moduleFixture },
        });
        expect(wrapper.find('h1').text()).toContain('Edit Module');
    });

    it('title input has aria-describedby="title-error" when form.errors.title is set', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            description: '',
            order_no: 1,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, module: moduleFixture },
        });
        expect(wrapper.html()).toContain('aria-describedby="title-error"');
    });

    it('shows role="alert" error banner when form.hasErrors is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            description: '',
            order_no: 1,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, module: moduleFixture },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            description: '',
            order_no: 1,
            published: false,
            errors: {},
            processing: true,
            hasErrors: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, module: moduleFixture },
        });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, {
            props: { section, module: moduleFixture },
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
