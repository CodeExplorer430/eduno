import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SubmissionRow from '@/Components/SubmissionRow.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Link: {
        template: '<a :aria-label="$attrs[\'aria-label\']" href="#"><slot /></a>',
        inheritAttrs: false,
    },
}));

const stubs = {
    Link: {
        template: '<a :aria-label="$attrs[\'aria-label\']" href="#"><slot /></a>',
        inheritAttrs: false,
    },
    Tag: { template: '<span>{{ value }}</span>', props: ['value', 'severity'] },
};

const baseSubmission = {
    id: 1,
    student: { id: 10, name: 'Maria Santos' },
    submitted_at: '2026-03-01T10:00:00Z',
    is_late: false,
    attempt_no: 1,
    status: 'submitted',
    grade: null,
};

const routeMock = vi.fn(() => '/');

const globalOpts = { stubs, mocks: { route: routeMock } };

describe('Components/SubmissionRow', () => {
    it('renders student name', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: baseSubmission, maxScore: 100 },
        });
        expect(wrapper.text()).toContain('Maria Santos');
    });

    it('<time> element exists with datetime attribute matching submitted_at', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: baseSubmission, maxScore: 100 },
        });
        const time = wrapper.find('time');
        expect(time.exists()).toBe(true);
        expect(time.attributes('datetime')).toBe('2026-03-01T10:00:00Z');
    });

    it('late badge is rendered when is_late is true', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: { ...baseSubmission, is_late: true }, maxScore: 100 },
        });
        expect(wrapper.text()).toContain('Late');
    });

    it('late badge is not rendered when is_late is false', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: baseSubmission, maxScore: 100 },
        });
        const spans = wrapper.findAll('span');
        const lateSpans = spans.filter((s) => s.text() === 'Late');
        expect(lateSpans.length).toBe(0);
    });

    it('view link aria-label contains the student name', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: baseSubmission, maxScore: 100 },
        });
        const link = wrapper.find('a[aria-label]');
        expect(link.attributes('aria-label')).toContain('Maria Santos');
    });

    it('score aria-label contains "Score:" and numeric score when grade exists', () => {
        const withGrade = {
            ...baseSubmission,
            status: 'graded',
            grade: { score: 85, released_at: '2026-03-05T00:00:00Z' },
        };
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: withGrade, maxScore: 100 },
        });
        const scoreSpan = wrapper.find('span[aria-label]');
        expect(scoreSpan.attributes('aria-label')).toContain('Score:');
        expect(scoreSpan.attributes('aria-label')).toContain('85');
    });

    it('score aria-label is "Not yet graded" when no grade', () => {
        const wrapper = mount(SubmissionRow, {
            global: globalOpts,
            props: { submission: baseSubmission, maxScore: 100 },
        });
        const scoreSpan = wrapper.find('span[aria-label]');
        expect(scoreSpan.attributes('aria-label')).toBe('Not yet graded');
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(SubmissionRow, {
            props: { submission: baseSubmission, maxScore: 100 },
            global: {
                mocks: { route: routeMock },
                stubs: {
                    ...stubs,
                    Tag: { template: '<span>{{ value }}</span>', props: ['value', 'severity'] },
                },
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
