import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Instructor/Announcements/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockUseForm = vi.fn(
    (initial: Record<string, unknown>): Record<string, unknown> => ({
        ...initial,
        errors: {},
        processing: false,
        hasErrors: false,
        delete: vi.fn(),
        reset: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: { template: '<a href="#"><slot /></a>' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a href="#" v-bind="$attrs"><slot /></a>',
        inheritAttrs: false,
    },
    Button: {
        template: '<button :aria-label="$attrs[\'aria-label\']"><slot /></button>',
        inheritAttrs: false,
    },
    Dialog: {
        template: '<div v-if="$attrs.visible !== false"><slot /><slot name="footer" /></div>',
        inheritAttrs: false,
    },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

const announcementFixture = {
    id: 1,
    title: 'Week 1 Introduction',
    body: 'Welcome to the course.',
    published_at: '2025-01-15T09:00:00Z',
    course_section: { section_name: 'A', course: { title: 'Intro to HCI' } },
};

describe('Instructor/Announcements/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('section has aria-labelledby="announcements-heading"', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        expect(wrapper.find('section[aria-labelledby="announcements-heading"]').exists()).toBe(
            true
        );
    });

    it('article has aria-labelledby="announcement-1-title" for announcement with id=1', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        expect(wrapper.find('article[aria-labelledby="announcement-1-title"]').exists()).toBe(true);
    });

    it('edit link has aria-label="Edit Week 1 Introduction"', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        expect(wrapper.find('a[aria-label="Edit Week 1 Introduction"]').exists()).toBe(true);
    });

    it('empty state has role="status" when announcements list is empty', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="status"]').exists()).toBe(true);
    });

    it('delete confirmation dialog is not shown initially', () => {
        const wrapper = mount(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        expect(wrapper.find('[role="dialog"]').exists()).toBe(false);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { announcements: [announcementFixture] },
            global: globalOpts,
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
