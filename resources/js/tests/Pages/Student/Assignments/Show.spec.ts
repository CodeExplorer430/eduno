import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Assignments/Show.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    Tag: { template: '<span class="tag">{{ value }}</span>', props: ['value', 'severity'] },
};

const baseAssignment = {
    id: 1,
    title: 'Lab Report',
    instructions: 'Write something',
    due_at: '2099-12-31T23:59:00Z',
    max_score: 100,
    allow_resubmission: false,
    course_section_id: 1,
};

const withSubmission = {
    id: 10,
    status: 'submitted',
    submitted_at: '2026-03-01T10:00:00Z',
    is_late: false,
    attempt_no: 1,
    grade: null,
};

const withGrade = {
    ...withSubmission,
    status: 'graded',
    grade: { score: 85, feedback: 'Good', released_at: '2026-03-05T00:00:00Z' },
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Student/Assignments/Show', () => {
    it('renders assignment title in an <h1>', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: null },
        });
        const h1 = wrapper.find('h1');
        expect(h1.exists()).toBe(true);
        expect(h1.text()).toContain('Lab Report');
    });

    it('"Submit Assignment" link exists when no prior submission', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: null },
        });
        expect(wrapper.text()).toContain('Submit Assignment');
    });

    it('"Submit Assignment" not rendered when submission exists and allow_resubmission is false', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: withSubmission },
        });
        expect(wrapper.text()).not.toContain('Submit Assignment');
        expect(wrapper.text()).not.toContain('Resubmit Assignment');
    });

    it('"Resubmit Assignment" shown when submission exists and allow_resubmission is true', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: {
                assignment: { ...baseAssignment, allow_resubmission: true },
                submission: withSubmission,
            },
        });
        expect(wrapper.text()).toContain('Resubmit Assignment');
    });

    it('grade section has role="status" when grade is released', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: withGrade },
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('grade section is absent when grade is not released', () => {
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: withSubmission },
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(false);
    });

    it('late indicator rendered when submission.is_late is true', () => {
        const lateSubmission = { ...withSubmission, is_late: true };
        const wrapper = mount(ShowPage, {
            global: globalOpts,
            props: { assignment: baseAssignment, submission: lateSubmission },
        });
        expect(wrapper.text()).toContain('Late Submission');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ShowPage, {
            props: { assignment: baseAssignment, submission: null },
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    Tag: {
                        template: '<span class="tag">{{ value }}</span>',
                        props: ['value', 'severity'],
                    },
                },
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
