import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ShowPage from '@/Pages/Student/Submissions/Show.vue';
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
    files: [],
    grade: null,
};

const withFiles = {
    ...baseSubmission,
    files: [{ id: 10, original_name: 'report.pdf', size_bytes: 2048 }],
};

const withGrade = {
    ...baseSubmission,
    status: 'graded',
    grade: { score: 85, feedback: 'Good', released_at: '2026-03-05T00:00:00Z' },
};

describe('Student/Submissions/Show', () => {
    it('renders assignment title in <h1>', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.find('h1').text()).toContain('Lab Report');
    });

    it('<ul aria-label="Submitted files"> exists when files are present', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: withFiles },
            global: globalOpts,
        });
        expect(wrapper.find('ul[aria-label="Submitted files"]').exists()).toBe(true);
    });

    it('shows "No files attached" text when files is empty', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('No files attached');
    });

    it('grade section has role="status" and aria-label="Grade result" when grade released', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: withGrade },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"][aria-label="Grade result"]').exists()).toBe(true);
    });

    it('grade section absent when grade not released', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.find('[aria-label="Grade result"]').exists()).toBe(false);
    });

    it('pending grade notice has role="status" when grade not released', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: baseSubmission },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"][aria-live="polite"]').exists()).toBe(true);
    });

    it('late submission indicator rendered when is_late is true', () => {
        const wrapper = mount(ShowPage, {
            props: { submission: { ...baseSubmission, is_late: true } },
            global: globalOpts,
        });
        expect(wrapper.text()).toContain('Late Submission');
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
