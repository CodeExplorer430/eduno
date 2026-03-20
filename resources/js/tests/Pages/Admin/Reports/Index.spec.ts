import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import ReportsIndex from '@/Pages/Admin/Reports/Index.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const report = {
    total_courses: 12,
    total_sections: 24,
    total_students: 480,
    total_submissions: 960,
    late_submissions: 48,
    graded_submissions: 720,
};

describe('Admin/Reports/Index', () => {
    it('renders the "System Reports" heading', () => {
        const wrapper = mount(ReportsIndex, {
            props: { report },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('System Reports');
    });

    it('renders total courses stat', () => {
        const wrapper = mount(ReportsIndex, {
            props: { report },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Total Courses');
        expect(wrapper.html()).toContain('12');
    });

    it('renders graded submissions stat', () => {
        const wrapper = mount(ReportsIndex, {
            props: { report },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Graded Submissions');
        expect(wrapper.html()).toContain('720');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(ReportsIndex, {
            props: { report },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
