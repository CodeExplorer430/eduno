import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Module/Index.vue';

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
        AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
        Link: {
            template: '<a v-bind="$attrs"><slot /></a>',
            inheritAttrs: false,
        },
        Modal: { template: '<div />' },
        Pagination: true,
    },
    mocks: { route: routeMock },
};

const sectionFixture = {
    id: 1,
    course_id: 1,
    section_name: 'A',
    instructor_id: 1,
    schedule_text: null,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
    course: {
        id: 1,
        code: 'CCS123',
        title: 'HCI',
        description: null,
        department: 'CS',
        term: '1st Semester',
        academic_year: '2025-2026',
        status: 'published' as const,
        created_by: 1,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
};

const moduleFixture = {
    id: 1,
    course_section_id: 1,
    title: 'Week 1: Introduction',
    description: 'Intro module',
    order_no: 1,
    published_at: null,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const props = {
    section: sectionFixture,
    modules: {
        data: [moduleFixture],
        links: [],
        meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
    },
    canManage: false,
};

describe('Module/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows module title', () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('Week 1: Introduction');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(IndexPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
