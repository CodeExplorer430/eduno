import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Announcement/Show.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    useForm: vi.fn(() => ({
        post: vi.fn(),
        delete: vi.fn(),
        processing: false,
        errors: {},
    })),
}));

const routeMock = vi.fn(() => '/');
const globalOpts = {
    stubs: {
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
        Modal: { template: '<div />' },
    },
    mocks: { route: routeMock },
};

const props = {
    announcement: {
        id: 1,
        course_section_id: 1,
        title: 'Test Announcement',
        body: 'Hello world body text.',
        published_at: '2026-01-01T00:00:00Z',
        created_by: 1,
        author: {
            id: 1,
            name: 'Dr. Smith',
            email: 'dr@example.com',
            role: 'instructor' as const,
            email_verified_at: null,
            created_at: '2026-01-01T00:00:00Z',
            updated_at: '2026-01-01T00:00:00Z',
        },
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
    canManage: false,
};

describe('Announcement/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows announcement title', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Test Announcement');
    });

    it('shows announcement body', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Hello world body text.');
    });

    it('breadcrumb has aria-label="Breadcrumb"', () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        expect(wrapper.find('nav[aria-label="Breadcrumb"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(ShowPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
