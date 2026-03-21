import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Instructor/Submissions/Show.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockPatch = vi.fn();

const mockUseForm = vi.fn(() => ({
    processing: false,
    patch: mockPatch,
}));

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: () => mockUseForm(),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: { template: '<a href="#"><slot /></a>' },
    Tag: { template: '<span>{{ value }}</span>', props: ['value', 'severity'] },
    GradeForm: { template: '<div />' },
    Button: {
        template:
            '<button type="submit" :disabled="disabled" :aria-busy="ariaBusy"><slot /></button>',
        props: ['disabled', 'ariaBusy'],
    },
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseSubmission = {
    id: 1,
    status: 'submitted',
    submitted_at: '2026-03-01T10:00:00Z',
    is_late: false,
    attempt_no: 1,
    assignment: { id: 1, title: 'Lab Report', max_score: 100 },
    student: { id: 10, name: 'Maria Santos' },
    files: [],
    grade: null,
};

const withUnreleasedGrade = {
    ...baseSubmission,
    status: 'graded',
    grade: { id: 5, score: 85, feedback: 'Good', released_at: null },
};

const withReleasedGrade = {
    ...baseSubmission,
    status: 'graded',
    grade: { id: 5, score: 85, feedback: 'Good', released_at: '2026-03-05T00:00:00Z' },
};

describe('Instructor/Submissions/Show', () => {
    it('renders without crashing', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders assignment title in <h1>', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.find('h1').text()).toContain('Lab Report');
    });

    it('<time> element exists with datetime matching submission.submitted_at', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        const time = wrapper.find('time');
        expect(time.exists()).toBe(true);
        expect(time.attributes('datetime')).toBe('2026-03-01T10:00:00Z');
    });

    it('late badge rendered when is_late is true', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: { ...baseSubmission, is_late: true } },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Late');
    });

    it('release grade button present when grade exists but released_at is null', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: withUnreleasedGrade },
            global: globalOpts,
        });
        const button = wrapper.find('button[type="submit"]');
        expect(button.exists()).toBe(true);
        expect(button.text()).toContain('Release Grade');
    });

    it('role="status" released notice shown when grade.released_at is set', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: withReleasedGrade },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"][aria-live="polite"]').exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
