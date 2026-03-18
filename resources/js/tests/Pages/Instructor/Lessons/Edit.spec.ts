import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Instructor/Lessons/Edit.vue';
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

const sectionFixture = { id: 1, course: { code: 'CCS123', title: 'Intro to HCI' } };
const moduleFixture = { id: 1, title: 'Module 1' };
const lessonFixture = {
    id: 1,
    title: 'Lesson 1',
    type: 'text',
    content: 'Some content',
    order_no: 1,
    published_at: null,
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Lessons/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('title input does not have aria-describedby when there are no errors', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        const input = wrapper.find('input#title');
        expect(input.attributes('aria-describedby')).toBeUndefined();
    });

    it('title input has aria-describedby="title-error" when form.errors.title is set', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 1,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        const input = wrapper.find('input#title');
        expect(input.attributes('aria-describedby')).toBe('title-error');
    });

    it('shows role="alert" error banner when form.hasErrors is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            type: 'text',
            content: '',
            order_no: 1,
            published: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: 'Lesson 1',
            type: 'text',
            content: '',
            order_no: 1,
            published: false,
            errors: {},
            processing: true,
            hasErrors: false,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, {
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    InputLabel: {
                        template: '<label :for="$props.for">{{ value }}</label>',
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
