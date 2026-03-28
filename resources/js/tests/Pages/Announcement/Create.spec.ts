import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CreatePage from '@/Pages/Announcement/Create.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn(() => ({
        title: '',
        body: '',
        post: vi.fn(),
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

describe('Announcement/Create', () => {
    it('renders without crashing', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('form has title and body fields', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.find('#title').exists()).toBe(true);
        expect(wrapper.find('#body').exists()).toBe(true);
    });

    it('breadcrumb has aria-label="Breadcrumb"', () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(CreatePage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
