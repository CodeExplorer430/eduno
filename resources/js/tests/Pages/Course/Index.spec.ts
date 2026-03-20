import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CourseIndex from '@/Pages/Course/Index.vue';
import type { Course } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
    usePage: () => ({
        props: { auth: { user: { name: 'Test User', email: 'test@example.com' } } },
    }),
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: {
        template: '<div><slot name="header" /><main id="main-content"><slot /></main></div>',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

function makeCourses(data: Course[]) {
    return {
        data,
        current_page: 1,
        last_page: 1,
        next_page_url: null,
        prev_page_url: null,
    };
}

const emptyCourses = makeCourses([]);

const sampleCourses = makeCourses([
    {
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
    },
    {
        id: 2,
        code: 'CS201',
        title: 'Data Structures',
        description: null,
        department: 'CCS',
        term: '1st Sem',
        academic_year: '2025-2026',
        status: 'draft',
        created_by: 1,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
]);

describe('Course/Index', () => {
    it('renders the "My Courses" heading', () => {
        const wrapper = mount(CourseIndex, {
            props: { courses: emptyCourses },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('My Courses');
    });

    it('renders empty state when no courses', () => {
        const wrapper = mount(CourseIndex, {
            props: { courses: emptyCourses },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('No courses found');
    });

    it('renders course items when courses are provided', () => {
        const wrapper = mount(CourseIndex, {
            props: { courses: sampleCourses },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Intro to CS');
        expect(wrapper.html()).toContain('Data Structures');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(CourseIndex, {
            props: { courses: emptyCourses },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
