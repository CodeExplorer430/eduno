import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Assignment/Edit.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn((initial: Record<string, unknown>) => ({
        ...initial,
        put: vi.fn(),
        processing: false,
        errors: {},
        wasSuccessful: false,
    })),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
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
        course_section_id: 1,
        title: 'Lab Report 1',
        instructions: 'Write a lab report.',
        due_at: '2099-12-31T00:00:00Z',
        max_score: 100,
        allow_resubmission: false,
        allowed_file_types: null,
        published_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
};

describe('Assignment/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows assignment title', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Lab Report 1');
    });

    it('breadcrumb has aria-label="Breadcrumb"', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
