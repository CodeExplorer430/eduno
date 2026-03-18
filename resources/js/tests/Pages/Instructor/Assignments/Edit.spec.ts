import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Instructor/Assignments/Edit.vue';
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
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const section = { id: 1, section_name: 'A' };
const assignment = {
    id: 5,
    title: 'Lab Report',
    instructions: null,
    due_at: '2026-04-01T23:59:00Z',
    max_score: 100,
    allow_resubmission: false,
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Assignments/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('title input has aria-describedby="assignment-title-error"', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        expect(wrapper.html()).toContain('aria-describedby="assignment-title-error"');
    });

    it('max score input has aria-describedby="assignment-max-score-error"', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        expect(wrapper.html()).toContain('aria-describedby="assignment-max-score-error"');
    });

    it('shows role="alert" error banner when form.hasErrors is true and errors exist', () => {
        mockUseForm.mockReturnValueOnce({
            title: 'Lab Report',
            instructions: '',
            due_at: '2026-04-01T23:59',
            max_score: '100',
            allow_resubmission: false,
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: 'Lab Report',
            instructions: '',
            due_at: '2026-04-01T23:59',
            max_score: '100',
            allow_resubmission: false,
            errors: {},
            processing: true,
            hasErrors: false,
            patch: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(EditPage, {
            global: globalOpts,
            props: { section, assignment },
        });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(EditPage, {
            props: { section, assignment },
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
