import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SubmissionsCreate from '@/Pages/Student/Submissions/Create.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {} as Record<string, string>,
        processing: false,
        wasSuccessful: false,
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
    FileUpload: { template: '<input type="file" aria-labelledby="files-label" />' },
    InputError: { template: '<span :id="$attrs.id" />', inheritAttrs: false },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
};

/** Shared global options — injects `route` via mocks so templates can call it. */
const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseAssignment = {
    id: 1,
    title: 'Homework 1',
    due_at: '2026-04-01T23:59:00Z',
    max_score: 100,
    allowed_file_types: null,
    course_section_id: 1,
};

describe('Student/Submissions/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders assignment.title in the page', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Homework 1');
    });

    it('breadcrumb nav has aria-label="Breadcrumb"', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('nav').attributes('aria-label')).toBe('Breadcrumb');
    });

    it('current page item has aria-current="page" and text "Submit"', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const currentItem = wrapper.find('[aria-current="page"]');
        expect(currentItem.exists()).toBe(true);
        expect(currentItem.text()).toBe('Submit');
    });

    it('displays due date in the dl metadata section', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const dlText = wrapper.find('dl').text();
        expect(dlText).not.toContain('No due date');
        expect(wrapper.find('dt').text()).toContain('Due Date');
    });

    it('displays max score in the dl metadata section', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('dl').text()).toContain('100 pts');
    });

    it('shows "No due date" when assignment.due_at is null', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: { ...baseAssignment, due_at: null } },
            global: globalOpts,
        });
        expect(wrapper.find('dl').text()).toContain('No due date');
    });

    it('submit button is disabled when no files are selected', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('submit button renders aria-busy="false" when not processing', () => {
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('button[type="submit"]').attributes('aria-busy')).toBe('false');
    });

    it('shows role="status" success banner when form.wasSuccessful is true', () => {
        mockUseForm.mockReturnValueOnce({
            files: [],
            errors: {},
            processing: false,
            wasSuccessful: true,
            hasErrors: false,
            post: vi.fn(),
        });
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('shows role="alert" error banner when form.hasErrors and no files error', () => {
        mockUseForm.mockReturnValueOnce({
            files: [],
            errors: {},
            processing: false,
            wasSuccessful: false,
            hasErrors: true,
            post: vi.fn(),
        });
        const wrapper = mount(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        expect(wrapper.find('[role="alert"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(SubmissionsCreate, {
            props: { assignment: baseAssignment },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
