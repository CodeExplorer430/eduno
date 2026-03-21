import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Instructor/Courses/Edit.vue';
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

const courseFixture = {
    id: 1,
    code: 'CCS123',
    title: 'Intro to HCI',
    description: null,
    department: 'CCS',
    term: '1st',
    academic_year: '2025-2026',
    status: 'active',
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Courses/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        expect(wrapper.exists()).toBe(true);
    });

    it('course code input has aria-describedby="course-code-error"', () => {
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        expect(wrapper.html()).toContain('aria-describedby="course-code-error"');
    });

    it('course title input has aria-describedby="course-title-error"', () => {
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        expect(wrapper.html()).toContain('aria-describedby="course-title-error"');
    });

    it('shows role="status" success banner when form.wasSuccessful is true', () => {
        mockUseForm.mockReturnValueOnce({
            code: 'CCS123',
            title: 'Intro to HCI',
            description: '',
            department: 'CCS',
            term: '1st',
            academic_year: '2025-2026',
            errors: {},
            processing: false,
            hasErrors: false,
            wasSuccessful: true,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('shows role="alert" error banner when form.hasErrors is true and errors exist', () => {
        mockUseForm.mockReturnValueOnce({
            code: 'CCS123',
            title: '',
            description: '',
            department: 'CCS',
            term: '1st',
            academic_year: '2025-2026',
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            wasSuccessful: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(EditPage, { global: globalOpts, props: { course: courseFixture } });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, {
            props: { course: courseFixture },
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
