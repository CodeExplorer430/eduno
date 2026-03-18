import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import UploadPage from '@/Pages/Instructor/Resources/Upload.vue';
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
    FileUpload: { template: '<div />' },
    Button: {
        template: '<button type="submit" :disabled="disabled"><slot /></button>',
        props: ['disabled'],
    },
};

const sectionFixture = { id: 1, course: { code: 'CCS123', title: 'Intro to HCI' } };
const moduleFixture = { id: 1, title: 'Module 1' };
const lessonFixture = { id: 1, title: 'Lesson 1' };

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Resources/Upload', () => {
    it('renders without crashing', () => {
        const wrapper = mount(UploadPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('breadcrumb nav has aria-label="Breadcrumb"', () => {
        const wrapper = mount(UploadPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('current breadcrumb item has aria-current="page" containing "Upload Resource"', () => {
        const wrapper = mount(UploadPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        const current = wrapper.find('[aria-current="page"]');
        expect(current.exists()).toBe(true);
        expect(current.text()).toContain('Upload Resource');
    });

    it('role="alert" error banner shown when form.hasErrors is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            file: null,
            visibility: 'enrolled',
            errors: { title: 'Required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(UploadPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('title input does NOT have aria-describedby when there are no errors', () => {
        const wrapper = mount(UploadPage, {
            global: globalOpts,
            props: { section: sectionFixture, module: moduleFixture, lesson: lessonFixture },
        });
        const input = wrapper.find('input#title');
        expect(input.attributes('aria-describedby')).toBeUndefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(UploadPage, {
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
                    Button: { template: '<button type="submit"><slot /></button>' },
                },
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
