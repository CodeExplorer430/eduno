import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import CourseShow from '@/Pages/Course/Show.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'aria-label'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        post: vi.fn(),
        delete: vi.fn(),
    }),
    usePage: () => ({
        props: { auth: { user: { id: 99, name: 'Student User', role: 'student' } } },
    }),
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: {
        template: '<div><slot name="header" /><main id="main-content"><slot /></main></div>',
    },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string, id?: unknown) =>
        id !== undefined ? `/${name}/${id}` : `/${name}`;
});

const course = {
    id: 1,
    code: 'CS101',
    title: 'Intro to CS',
    description: 'An introductory course.',
    department: 'CCS',
    term: '1st Sem',
    academic_year: '2025-2026',
    status: 'published' as const,
    created_by: 1,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
    creator: {
        id: 1,
        name: 'Dr. Santos',
        email: 'santos@ucc.edu.ph',
        role: 'instructor' as const,
        email_verified_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
    sections: [
        {
            id: 1,
            course_id: 1,
            section_name: 'Section A',
            block_code: 'BSCS-2A',
            instructor_id: 1,
            schedule_text: 'MWF 9:00–10:00 AM',
            created_at: '2026-01-01T00:00:00Z',
            updated_at: '2026-01-01T00:00:00Z',
            instructor: {
                id: 1,
                name: 'Dr. Santos',
                email: 'santos@ucc.edu.ph',
                role: 'instructor' as const,
                email_verified_at: null,
                created_at: '2026-01-01T00:00:00Z',
                updated_at: '2026-01-01T00:00:00Z',
            },
            enrollments: [],
        },
    ],
};

describe('Course/Show', () => {
    it('renders section name and block_code', () => {
        const wrapper = mount(CourseShow, {
            props: { course },
            global: {
                mocks: {
                    route: (name: string, id?: unknown) =>
                        id !== undefined ? `/${name}/${id}` : `/${name}`,
                },
            },
        });
        expect(wrapper.html()).toContain('Section A');
        expect(wrapper.html()).toContain('BSCS-2A');
    });

    it('renders the class block helper text', () => {
        const wrapper = mount(CourseShow, {
            props: { course },
            global: {
                mocks: {
                    route: (name: string, id?: unknown) =>
                        id !== undefined ? `/${name}/${id}` : `/${name}`,
                },
            },
        });
        expect(wrapper.html()).toContain(
            'Sections correspond to your enrolled class block (e.g., BSCS-2A).'
        );
    });

    it('has no axe violations', async () => {
        const wrapper = mount(CourseShow, {
            props: { course },
            global: {
                mocks: {
                    route: (name: string, id?: unknown) =>
                        id !== undefined ? `/${name}/${id}` : `/${name}`,
                },
            },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
