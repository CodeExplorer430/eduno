import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import RosterIndex from '@/Pages/Instructor/Roster/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: () => ({
        email: '',
        errors: {},
        processing: false,
        reset: vi.fn(),
        post: vi.fn(),
        delete: vi.fn(),
    }),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Breadcrumb: { template: '<nav />' },
    EmptyState: { template: '<div data-testid="empty-state"><slot /></div>' },
    Head: true,
};

const globalOpts = {
    stubs,
    mocks: { route: vi.fn(() => '/') },
};

const baseSection = {
    id: 1,
    section_name: 'A',
    course: { id: 1, code: 'CCS101', title: 'Intro to HCI' },
};

const baseStudents = [
    {
        enrollment_id: 10,
        id: 1,
        name: 'Alice Santos',
        email: 'alice@example.com',
        enrolled_at: '2026-01-15T00:00:00Z',
    },
    {
        enrollment_id: 11,
        id: 2,
        name: 'Bob Cruz',
        email: 'bob@example.com',
        enrolled_at: '2026-01-16T00:00:00Z',
    },
];

const baseProps = { section: baseSection, students: baseStudents };

describe('Instructor/Roster/Index', () => {
    it('renders section name and course code in heading', () => {
        const wrapper = mount(RosterIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('CCS101');
        expect(wrapper.text()).toContain('Section A');
    });

    it('renders student rows with name and email', () => {
        const wrapper = mount(RosterIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.text()).toContain('Alice Santos');
        expect(wrapper.text()).toContain('alice@example.com');
        expect(wrapper.text()).toContain('Bob Cruz');
        expect(wrapper.text()).toContain('bob@example.com');
    });

    it('client-side search filters the table', async () => {
        const wrapper = mount(RosterIndex, { props: baseProps, global: globalOpts });
        const input = wrapper.find('input[type="search"]');
        await input.setValue('alice');
        expect(wrapper.text()).toContain('Alice Santos');
        expect(wrapper.text()).not.toContain('Bob Cruz');
    });

    it('renders empty state when no students', () => {
        const wrapper = mount(RosterIndex, {
            props: { ...baseProps, students: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[data-testid="empty-state"]').exists()).toBe(true);
    });

    it('remove button has correct aria-label', () => {
        const wrapper = mount(RosterIndex, { props: baseProps, global: globalOpts });
        expect(wrapper.find('button[aria-label="Remove Alice Santos"]').exists()).toBe(true);
    });

    it('enroll form has labeled email input', () => {
        const wrapper = mount(RosterIndex, { props: baseProps, global: globalOpts });
        const input = wrapper.find('#enroll-email');
        expect(input.exists()).toBe(true);
        const label = wrapper.find('label[for="enroll-email"]');
        expect(label.exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(RosterIndex, {
            props: baseProps,
            global: globalOpts,
        });
        const results = await axe(wrapper.element, {
            rules: { region: { enabled: false } },
        });
        expect(results).toHaveNoViolations();
    });
});
