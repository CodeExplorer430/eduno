import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SubmissionShow from '@/Pages/Submission/Show.vue';
import type { Submission } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: (initial: Record<string, unknown>) => ({
        ...initial,
        processing: false,
        errors: {},
        post: vi.fn(),
        patch: vi.fn(),
    }),
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const baseSubmission: Submission = {
    id: 1,
    assignment_id: 1,
    student_id: 1,
    status: 'submitted',
    submitted_at: '2026-03-01T10:00:00Z',
    is_late: false,
    attempt_no: 1,
    files: [],
    assignment: {
        id: 1,
        title: 'Lab Report 1',
        course_section_id: 1,
        max_score: 100,
        instructions: null,
        due_at: null,
        allow_resubmission: false,
        allowed_file_types: null,
        published_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
    student: {
        id: 1,
        name: 'Juan dela Cruz',
        email: 'juan@example.com',
        role: 'student',
        email_verified_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
    grade: undefined,
    created_at: '2026-03-01T10:00:00Z',
    updated_at: '2026-03-01T10:00:00Z',
};

describe('Submission/Show', () => {
    it('renders student name in the heading', () => {
        const wrapper = mount(SubmissionShow, {
            props: { submission: baseSubmission, isInstructor: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Juan dela Cruz');
    });

    it('shows "not released yet" status when grade is null (student view)', () => {
        const wrapper = mount(SubmissionShow, {
            props: { submission: baseSubmission, isInstructor: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('not been released yet');
    });

    it('renders the grade section when grade is present and released (student view)', () => {
        const submission: Submission = {
            ...baseSubmission,
            grade: {
                id: 1,
                submission_id: 1,
                graded_by: 2,
                score: 90,
                feedback: 'Great work!',
                released_at: '2026-03-10T00:00:00Z',
                created_at: '2026-03-10T00:00:00Z',
                updated_at: '2026-03-10T00:00:00Z',
            },
        };
        const wrapper = mount(SubmissionShow, {
            props: { submission, isInstructor: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Your Grade');
        expect(wrapper.html()).toContain('90');
    });

    it('renders the grade form for instructors', () => {
        const wrapper = mount(SubmissionShow, {
            props: { submission: baseSubmission, isInstructor: true },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Grade');
        expect(wrapper.find('form').exists()).toBe(true);
    });

    it('has no axe violations (student view)', async () => {
        const wrapper = mount(SubmissionShow, {
            props: { submission: baseSubmission, isInstructor: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
