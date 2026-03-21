import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SubmissionIndex from '@/Pages/Submission/Index.vue';
import type { Assignment, PaginatedResponse, Submission } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
}));

vi.mock('@/Components/Pagination.vue', () => ({
    default: { template: '<nav aria-label="Pagination"></nav>' },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const assignment: Assignment = {
    id: 1,
    course_section_id: 1,
    title: 'Lab Report 1',
    instructions: null,
    due_at: '2026-04-01T23:59:00Z',
    max_score: 100,
    allow_resubmission: false,
    allowed_file_types: null,
    published_at: '2026-03-01T00:00:00Z',
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const emptySubmissions: PaginatedResponse<Submission> = {
    data: [],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
};

const lateSubmission: Submission = {
    id: 1,
    assignment_id: 1,
    student_id: 2,
    status: 'submitted',
    submitted_at: '2026-04-02T10:00:00Z',
    is_late: true,
    attempt_no: 1,
    files: [],
    student: {
        id: 2,
        name: 'Maria Santos',
        email: 'maria@example.com',
        role: 'student',
        email_verified_at: null,
        created_at: '2026-01-01T00:00:00Z',
        updated_at: '2026-01-01T00:00:00Z',
    },
    created_at: '2026-04-02T10:00:00Z',
    updated_at: '2026-04-02T10:00:00Z',
};

describe('Submission/Index', () => {
    it('renders the "Submissions" heading', () => {
        const wrapper = mount(SubmissionIndex, {
            props: { assignment, submissions: emptySubmissions, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Submissions');
    });

    it('renders empty state when no submissions', () => {
        const wrapper = mount(SubmissionIndex, {
            props: { assignment, submissions: emptySubmissions, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('No submissions yet');
    });

    it('renders submissions table with student name', () => {
        const wrapper = mount(SubmissionIndex, {
            props: {
                assignment,
                submissions: {
                    data: [lateSubmission],
                    links: [],
                    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
                },
                canManage: false,
            },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Maria Santos');
    });

    it('renders late badge for late submissions', () => {
        const wrapper = mount(SubmissionIndex, {
            props: {
                assignment,
                submissions: {
                    data: [lateSubmission],
                    links: [],
                    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
                },
                canManage: false,
            },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Late');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(SubmissionIndex, {
            props: { assignment, submissions: emptySubmissions, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
