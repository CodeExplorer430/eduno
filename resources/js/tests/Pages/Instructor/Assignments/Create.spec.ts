import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Instructor/Assignments/Create.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

let lastForm: { allowed_file_types: string[]; [key: string]: unknown };

const mockUseForm = vi.fn((initial: Record<string, unknown>): Record<string, unknown> => {
    lastForm = {
        ...initial,
        allowed_file_types: (initial.allowed_file_types as string[]) ?? [],
        errors: {} as Record<string, string>,
        processing: false,
        hasErrors: false,
        post: vi.fn(),
        reset: vi.fn(),
    };
    return lastForm;
});

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

const section = { id: 1, section_name: 'A', course: { title: 'Intro to HCI' } };

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Instructor/Assignments/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.exists()).toBe(true);
    });

    it('title input has aria-describedby="assignment-title-error"', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.html()).toContain('aria-describedby="assignment-title-error"');
    });

    it('max score input has aria-describedby="assignment-max-score-error"', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.html()).toContain('aria-describedby="assignment-max-score-error"');
    });

    it('renders the allowed file types fieldset', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.find('fieldset').exists()).toBe(true);
        expect(wrapper.find('legend').text()).toContain('Accepted File Types');
    });

    it('shows role="alert" error banner when form.hasErrors is true and errors exist', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            instructions: '',
            due_at: '',
            max_score: '100',
            allow_resubmission: false,
            allowed_file_types: [],
            errors: { title: 'The title field is required.' },
            processing: false,
            hasErrors: true,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('submit button has aria-busy="false" when not processing', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('aria-busy')).toBe('false');
    });

    it('submit button is disabled when form.processing is true', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            instructions: '',
            due_at: '',
            max_score: '100',
            allow_resubmission: false,
            allowed_file_types: [],
            errors: {},
            processing: true,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();
    });

    it('renders 7 file type chips', () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const chips = wrapper.findAll('input[type="checkbox"][class="sr-only"]');
        expect(chips).toHaveLength(7);
    });

    it('selecting "Image (PNG/JPG)" adds both image/png and image/jpeg', async () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const checkbox = wrapper.find('input[id="file-type-Image (PNG/JPG)"]');
        await checkbox.trigger('change');
        expect(lastForm.allowed_file_types).toContain('image/png');
        expect(lastForm.allowed_file_types).toContain('image/jpeg');
    });

    it('selecting "Word (.doc/.docx)" adds both msword and docx mimes', async () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const checkbox = wrapper.find('input[id="file-type-Word (.doc/.docx)"]');
        await checkbox.trigger('change');
        expect(lastForm.allowed_file_types).toContain('application/msword');
        expect(lastForm.allowed_file_types).toContain(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
    });

    it('selecting "PDF" adds only application/pdf', async () => {
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const checkbox = wrapper.find('input[id="file-type-PDF"]');
        await checkbox.trigger('change');
        expect(lastForm.allowed_file_types).toContain('application/pdf');
        expect(lastForm.allowed_file_types).toHaveLength(1);
    });

    it('deselecting "Image (PNG/JPG)" when both mimes are present removes both', async () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            instructions: '',
            due_at: '',
            max_score: '100',
            allow_resubmission: false,
            allowed_file_types: ['image/png', 'image/jpeg'],
            errors: {},
            processing: false,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        mount(CreatePage, { global: globalOpts, props: { section } });
        const checkbox = document.querySelector('input[id="file-type-Image (PNG/JPG)"]');
        (checkbox as HTMLInputElement)?.dispatchEvent(new Event('change'));
        const types = lastForm.allowed_file_types;
        expect(types).not.toContain('image/png');
        expect(types).not.toContain('image/jpeg');
    });

    it('isSelected is true when only image/png is present (backward compat)', () => {
        mockUseForm.mockReturnValueOnce({
            title: '',
            instructions: '',
            due_at: '',
            max_score: '100',
            allow_resubmission: false,
            allowed_file_types: ['image/png'],
            errors: {},
            processing: false,
            hasErrors: false,
            post: vi.fn(),
            reset: vi.fn(),
        });
        const wrapper = mount(CreatePage, { global: globalOpts, props: { section } });
        const imageLabel = wrapper
            .findAll('label')
            .find((l) => l.text().includes('Image (PNG/JPG)'));
        expect(imageLabel?.classes()).toContain('text-blue-700');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(CreatePage, {
            props: { section },
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
