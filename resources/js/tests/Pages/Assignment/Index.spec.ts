import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AssignmentIndex from '@/Pages/Assignment/Index.vue';
import type { CourseSection, PaginatedResponse, Assignment, Submission } from '@/Types/models';

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
    usePage: () => ({
        props: { auth: { user: { name: 'Test', email: 'test@example.com' } } },
    }),
}));

vi.mock('@/Components/Pagination.vue', () => ({
    default: { template: '<nav aria-label="Pagination"></nav>' },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const section = {
    id: 1,
    section_name: 'CS101-A',
    block_code: null,
    course_id: 1,
    instructor_id: 1,
    schedule_text: null,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
} as CourseSection;

const emptyAssignments = {
    data: [],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
} as PaginatedResponse<Assignment>;

const sampleAssignments = {
    data: [
        {
            id: 1,
            title: 'Lab Report 1',
            course_section_id: 1,
            due_at: '2026-04-01T23:59:00Z',
            published_at: '2026-03-01T00:00:00Z',
            max_score: 100,
            instructions: null,
            allow_resubmission: false,
            mySubmission: null,
            created_at: '2026-01-01T00:00:00Z',
            updated_at: '2026-01-01T00:00:00Z',
        },
    ],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
} as PaginatedResponse<Assignment>;

const sampleSubmission: Submission = {
    id: 10,
    assignment_id: 1,
    student_id: 2,
    status: 'submitted',
    submitted_at: '2026-03-20T14:00:00Z',
    is_late: false,
    attempt_no: 1,
    created_at: '2026-03-20T14:00:00Z',
    updated_at: '2026-03-20T14:00:00Z',
};

const assignmentsWithSubmission = {
    data: [
        {
            ...sampleAssignments.data[0],
            mySubmission: sampleSubmission,
        },
    ],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
} as PaginatedResponse<Assignment>;

describe('Assignment/Index', () => {
    it('renders the "Assignments" heading', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: emptyAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Assignments');
    });

    it('renders empty state when no assignments', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: emptyAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('No assignments yet');
    });

    it('renders due date column when assignments are present', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: sampleAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Due Date');
        expect(wrapper.html()).toContain('Lab Report 1');
    });

    it('shows "My Submission" column header for student view', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: sampleAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('My Submission');
    });

    it('does not show "My Submission" column header for instructor view', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: sampleAssignments, canManage: true },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).not.toContain('My Submission');
    });

    it('shows submission status and date when mySubmission is present', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: assignmentsWithSubmission, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('submitted');
        expect(wrapper.html()).toContain('Mar 20');
    });

    it('shows "—" when mySubmission is null', () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: sampleAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('—');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(AssignmentIndex, {
            props: { section, assignments: emptyAssignments, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
