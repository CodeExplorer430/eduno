import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Lesson/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn(() => ({
        title: '',
        content: '',
        type: 'text',
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
    module: {
        id: 1,
        course_section_id: 1,
        title: 'Week 1: Introduction',
        description: 'Intro module',
        order_no: 1,
        published_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
};

describe('Lesson/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows Create Lesson heading', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Create Lesson');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
