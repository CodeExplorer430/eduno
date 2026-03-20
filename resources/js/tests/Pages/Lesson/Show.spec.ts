import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import LessonShow from '@/Pages/Lesson/Show.vue';
import type { Course, CourseSection, Module, Resource } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        hasErrors: false,
        errors: {},
        post: vi.fn(),
        delete: vi.fn(),
        reset: vi.fn(),
    }),
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: {
        template: '<div><slot name="header" /><main id="main-content"><slot /></main></div>',
    },
}));

vi.mock('@/Components/Breadcrumb.vue', () => ({
    default: {
        props: ['crumbs'],
        template: '<nav aria-label="Breadcrumb"></nav>',
    },
}));

vi.mock('@/composables/useFileSize', () => ({
    useFileSize: () => ({ formatBytes: (n: number) => `${n} B` }),
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

const module: Module = {
    id: 1,
    course_section_id: 1,
    title: 'Module 1',
    description: null,
    order_no: 1,
    published_at: null,
    section,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const baseLesson: any = {
    id: 1,
    module_id: 1,
    title: 'Lesson 1: Introduction',
    content: null,
    type: 'text' as const,
    order_no: 1,
    published_at: null,
    module: { ...module, section: { ...section, course } },
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const sampleResource: Resource = {
    id: 1,
    lesson_id: 1,
    title: 'Lecture Slides',
    file_path: 'resources/slides.pdf',
    mime_type: 'application/pdf',
    size_bytes: 102400,
    visibility: 'enrolled',
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

describe('Lesson/Show', () => {
    it('renders the lesson title', () => {
        const wrapper = mount(LessonShow, {
            props: { lesson: baseLesson, resources: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Lesson 1: Introduction');
    });

    it('renders the Resources section heading', () => {
        const wrapper = mount(LessonShow, {
            props: { lesson: baseLesson, resources: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Resources');
    });

    it('renders resource title when resources are present', () => {
        const wrapper = mount(LessonShow, {
            props: { lesson: baseLesson, resources: [sampleResource], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Lecture Slides');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(LessonShow, {
            props: { lesson: baseLesson, resources: [], canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
