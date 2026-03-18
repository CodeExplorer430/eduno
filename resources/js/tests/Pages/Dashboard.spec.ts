import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Dashboard from '@/Pages/Dashboard.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
};

/** Shared global options — injects `route` via mocks so templates can call it. */
const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

// ── Student props ────────────────────────────────────────────────────────────

const studentProps = {
    role: 'student' as const,
    enrolled_courses_count: 3,
    upcoming_assignments: [
        {
            id: 1,
            course_section_id: 1,
            title: 'Lab Report 1',
            instructions: null,
            due_at: '2026-04-05T23:59:00Z',
            max_score: 50,
            allow_resubmission: false,
            published_at: '2026-03-01T00:00:00Z',
            created_at: '2026-03-01T00:00:00Z',
            updated_at: '2026-03-01T00:00:00Z',
        },
    ],
    recent_announcements: [
        {
            id: 1,
            course_section_id: 1,
            title: 'Welcome to the course',
            body: 'Hello everyone!',
            published_at: '2026-03-10T00:00:00Z',
            created_by: 2,
            created_at: '2026-03-10T00:00:00Z',
            updated_at: '2026-03-10T00:00:00Z',
            course_section: {
                id: 1,
                course_id: 1,
                section_name: 'A',
                instructor_id: 2,
                schedule_text: null,
                created_at: '2026-01-01T00:00:00Z',
                updated_at: '2026-01-01T00:00:00Z',
                course: {
                    id: 1,
                    code: 'CCS101',
                    title: 'Intro to Computing',
                    description: null,
                    department: 'CCS',
                    term: '2nd Semester',
                    academic_year: '2025-2026',
                    status: 'published' as const,
                    created_by: 1,
                    created_at: '2026-01-01T00:00:00Z',
                    updated_at: '2026-01-01T00:00:00Z',
                },
            },
            author: {
                id: 2,
                name: 'Prof. Santos',
                email: 'prof.santos@example.com',
                role: 'instructor' as const,
                email_verified_at: null,
                created_at: '2026-01-01T00:00:00Z',
                updated_at: '2026-01-01T00:00:00Z',
            },
        },
    ],
    latest_grade: null,
};

// ── Instructor props ─────────────────────────────────────────────────────────

const instructorProps = {
    role: 'instructor' as const,
    courses_count: 5,
    pending_submissions_count: 2,
    recent_submissions: [
        {
            id: 1,
            assignment_id: 1,
            student_id: 3,
            status: 'submitted' as const,
            submitted_at: '2026-03-17T10:00:00Z',
            is_late: false,
            attempt_no: 1,
            created_at: '2026-03-17T10:00:00Z',
            updated_at: '2026-03-17T10:00:00Z',
            assignment: {
                id: 1,
                course_section_id: 1,
                title: 'Lab Report 1',
                instructions: null,
                due_at: '2026-04-05T23:59:00Z',
                max_score: 50,
                allow_resubmission: false,
                published_at: '2026-03-01T00:00:00Z',
                created_at: '2026-03-01T00:00:00Z',
                updated_at: '2026-03-01T00:00:00Z',
            },
            student: { id: 3, name: 'Maria Reyes' },
        },
    ],
    upcoming_deadlines: [],
};

// ── Admin props ──────────────────────────────────────────────────────────────

const adminProps = {
    role: 'admin' as const,
    users_by_role: { student: 40, instructor: 5, admin: 1 },
    total_courses: 10,
    total_submissions: 80,
    total_grades_released: 60,
};

// ── Student role ─────────────────────────────────────────────────────────────

describe('Dashboard — student role', () => {
    it('renders without crashing', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('shows enrolled courses count in the stats dl', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('3');
    });

    it('shows upcoming assignments section heading', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Upcoming Assignments');
    });

    it('shows "None yet" when latest_grade is null', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('None yet');
    });

    it('shows assignment title and due date in upcoming assignments list', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Lab Report 1');
    });

    it('shows announcement title in recent announcements', () => {
        const wrapper = mount(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Welcome to the course');
    });

    it('shows "No assignments due" empty state when upcoming_assignments is empty', () => {
        const wrapper = mount(Dashboard, {
            props: { ...studentProps, upcoming_assignments: [] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('No assignments due in the next 7 days.');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(Dashboard, {
            props: studentProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});

// ── Instructor role ──────────────────────────────────────────────────────────

describe('Dashboard — instructor role', () => {
    it('renders courses count', () => {
        const wrapper = mount(Dashboard, {
            props: instructorProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('5');
    });

    it('renders pending submissions count', () => {
        const wrapper = mount(Dashboard, {
            props: instructorProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('2');
    });

    it('shows recent submission student name and assignment title', () => {
        const wrapper = mount(Dashboard, {
            props: instructorProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Maria Reyes');
        expect(wrapper.text()).toContain('Lab Report 1');
    });

    it('shows "No recent submissions" empty state when list is empty', () => {
        const wrapper = mount(Dashboard, {
            props: { ...instructorProps, recent_submissions: [] },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('No recent submissions.');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(Dashboard, {
            props: instructorProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});

// ── Admin role ───────────────────────────────────────────────────────────────

describe('Dashboard — admin role', () => {
    it('renders total_courses stat', () => {
        const wrapper = mount(Dashboard, {
            props: adminProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('10');
    });

    it('renders users_by_role.student count in Users by Role section', () => {
        const wrapper = mount(Dashboard, {
            props: adminProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('40');
    });

    it('renders total_grades_released', () => {
        const wrapper = mount(Dashboard, {
            props: adminProps,
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('60');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(Dashboard, {
            props: adminProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
