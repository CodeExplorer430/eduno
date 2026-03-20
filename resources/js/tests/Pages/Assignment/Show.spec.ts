import { describe, it, expect, vi, beforeAll } from 'vitest';
import { nextTick } from 'vue';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AssignmentShow from '@/Pages/Assignment/Show.vue';
import type { Assignment } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: { props: ['href'], template: '<a :href="href"><slot /></a>' },
    useForm: () => ({ processing: false, errors: {}, post: vi.fn() }),
    router: { post: vi.fn() },
}));

vi.mock('@/Components/Breadcrumb.vue', () => ({
    default: { template: '<nav />' },
}));

vi.mock('@/Components/StatusBadge.vue', () => ({
    default: { props: ['variant'], template: '<span />' },
}));

vi.mock('@/composables/useFileSize', () => ({
    useFileSize: () => ({
        formatBytes: (bytes: number) => `${bytes} B`,
    }),
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const baseAssignment: Assignment = {
    id: 1,
    course_section_id: 1,
    title: 'Final Project Report',
    instructions: 'Submit your final report here.',
    due_at: '2026-05-01T23:59:00Z',
    max_score: 100,
    allow_resubmission: false,
    published_at: '2026-03-01T00:00:00Z',
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

describe('Assignment/Show', () => {
    it('renders assignment title', () => {
        const wrapper = mount(AssignmentShow, {
            props: { assignment: baseAssignment, canManage: false, mySubmission: null },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain(baseAssignment.title);
    });

    it('shows 1-attempt warning when allow_resubmission is false', () => {
        const wrapper = mount(AssignmentShow, {
            props: {
                assignment: { ...baseAssignment, allow_resubmission: false },
                canManage: false,
                mySubmission: null,
            },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('1 attempt');
    });

    it('hides 1-attempt warning when allow_resubmission is true', () => {
        const wrapper = mount(AssignmentShow, {
            props: {
                assignment: { ...baseAssignment, allow_resubmission: true },
                canManage: false,
                mySubmission: null,
            },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).not.toContain('1 attempt');
    });

    it('does not render progress bar initially', () => {
        const wrapper = mount(AssignmentShow, {
            props: { assignment: baseAssignment, canManage: false, mySubmission: null },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.find('[role="progressbar"]').exists()).toBe(false);
    });

    it('shows file count and total size after files are selected', async () => {
        const wrapper = mount(AssignmentShow, {
            props: { assignment: baseAssignment, canManage: false, mySubmission: null },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });

        const input = wrapper.find('input[type="file"]');
        const file1 = new File(['a'], 'report.pdf', { type: 'application/pdf' });
        const file2 = new File(['bb'], 'data.zip', { type: 'application/zip' });
        Object.defineProperty(input.element, 'files', {
            value: [file1, file2],
            configurable: true,
        });
        await input.trigger('change');
        await nextTick();

        expect(wrapper.html()).toContain('2 file(s) selected');
        expect(wrapper.html()).toContain('3 B');
    });

    it('does not show file summary when no files are selected', () => {
        const wrapper = mount(AssignmentShow, {
            props: { assignment: baseAssignment, canManage: false, mySubmission: null },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).not.toContain('file(s) selected');
    });

    it('has no axe violations (student view)', async () => {
        const wrapper = mount(AssignmentShow, {
            props: { assignment: baseAssignment, canManage: false, mySubmission: null },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
