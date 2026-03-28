import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Student/Submissions/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn(() => ({
        files: [],
        post: vi.fn(),
        processing: false,
        errors: {},
        wasSuccessful: false,
        hasErrors: false,
    })),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        FileUploadInput: { template: '<div />' },
        InputError: { template: '<span />' },
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
    },
    mocks: { route: routeMock },
};

const props = {
    assignment: {
        id: 1,
        title: 'Lab Report',
        due_at: '2099-12-31T00:00:00Z',
        max_score: 100,
        allowed_file_types: ['application/pdf'],
        course_section_id: 1,
    },
};

describe('Student/Submissions/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows assignment title', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Lab Report');
    });

    it('submit button is present', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
