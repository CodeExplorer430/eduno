import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ModuleShow from '@/Pages/Module/Show.vue';
import type { Course, CourseSection, Lesson } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        post: vi.fn(),
    }),
    router: { delete: vi.fn() },
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: {
        template: '<div><slot name="header" /><main id="main-content"><slot /></main></div>',
    },
}));

vi.mock('@/Components/Modal.vue', () => ({
    default: {
        props: ['show', 'maxWidth', 'labelledby'],
        template: '<div v-if="show"><slot /></div>',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const course: Course = {
    id: 1,
    code: 'CS101',
    title: 'Intro to CS',
    description: null,
    department: 'CCS',
    term: '1st Sem',
    academic_year: '2025-2026',
    status: 'published',
    created_by: 1,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const section: CourseSection = {
    id: 1,
    section_name: 'CS101-A',
    block_code: null,
    course_id: 1,
    instructor_id: 1,
    schedule_text: null,
    course,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const baseModule: any = {
    id: 1,
    course_section_id: 1,
    title: 'Module 1: Foundations',
    description: null,
    order_no: 1,
    published_at: null,
    section,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const sampleLesson: Lesson = {
    id: 1,
    module_id: 1,
    title: 'Introduction to HCI',
    content: null,
    type: 'text',
    order_no: 1,
    published_at: null,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

describe('Module/Show', () => {
    it('renders the module title', () => {
        const wrapper = mount(ModuleShow, {
            props: { module: baseModule, lessons: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Module 1: Foundations');
    });

    it('renders the Lessons section heading', () => {
        const wrapper = mount(ModuleShow, {
            props: { module: baseModule, lessons: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Lessons');
    });

    it('renders lesson title when lessons are present', () => {
        const wrapper = mount(ModuleShow, {
            props: { module: baseModule, lessons: [sampleLesson], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Introduction to HCI');
    });

    it('renders Publish button for instructors', () => {
        const wrapper = mount(ModuleShow, {
            props: { module: baseModule, lessons: [], canManage: true },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Publish');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(ModuleShow, {
            props: { module: baseModule, lessons: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
