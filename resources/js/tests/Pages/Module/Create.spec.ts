import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Module/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn(() => ({
        title: '',
        description: '',
        order_no: null,
        post: vi.fn(),
        processing: false,
        errors: {},
    })),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
    },
    mocks: { route: routeMock },
};

const props = {
    section: {
        id: 1,
        course_id: 1,
        section_name: 'A',
        instructor_id: 1,
        schedule_text: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
};

describe('Module/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows Create Module heading', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Create Module');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
