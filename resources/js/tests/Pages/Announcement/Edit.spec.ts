import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import EditPage from '@/Pages/Announcement/Edit.vue';

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
    announcement: {
        id: 1,
        course_section_id: 1,
        title: 'Hello World',
        body: 'Announcement body.',
        published_at: null,
        created_by: 1,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
};

describe('Announcement/Edit', () => {
    it('renders without crashing', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('pre-fills title field with announcement.title', () => {
        const wrapper = mount(EditPage, { props, global: globalOpts });
        const titleInput = wrapper.find('#title');
        expect((titleInput.element as HTMLInputElement).value).toBe('Hello World');
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
