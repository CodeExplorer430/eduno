import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Module/Edit.vue';

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

describe('Module/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows Edit Module heading', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Edit Module');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
