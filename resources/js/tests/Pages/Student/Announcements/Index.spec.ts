import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import IndexPage from '@/Pages/Student/Announcements/Index.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    Link: {
        template: '<a :href="$attrs.href || \'#\'"><slot /></a>',
        inheritAttrs: false,
    },
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Head: true,
    Link: {
        template: '<a :href="$attrs.href || \'#\'"><slot /></a>',
        inheritAttrs: false,
    },
};

const globalOpts = { stubs };

const announcementFixture = {
    id: 1,
    title: 'Welcome to the course',
    body: 'This is a test announcement.',
    published_at: '2025-01-15T09:00:00Z',
    course_section: {
        id: 1,
        section_name: 'A',
        course: { id: 1, code: 'CCS123', title: 'Intro to HCI' },
    },
    author: { id: 1, name: 'Prof. Smith' },
};

const paginationLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '/?page=1', label: '1', active: true },
    { url: '/?page=2', label: '2', active: false },
    { url: '/?page=2', label: 'Next &raquo;', active: false },
];

describe('Student/Announcements/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { announcements: { data: [], links: [] } },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('feed container has role="feed" and aria-label="Course announcements" when data present', () => {
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { announcements: { data: [announcementFixture], links: [] } },
        });
        const feed = wrapper.find('[role="feed"]');
        expect(feed.exists()).toBe(true);
        expect(feed.attributes('aria-label')).toBe('Course announcements');
    });

    it('article has aria-labelledby="announcement-title-1" for announcement with id=1', () => {
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { announcements: { data: [announcementFixture], links: [] } },
        });
        const article = wrapper.find('article[aria-labelledby="announcement-title-1"]');
        expect(article.exists()).toBe(true);
    });

    it('shows "No announcements yet." when announcements.data is empty', () => {
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: { announcements: { data: [], links: [] } },
        });
        expect(wrapper.text()).toContain('No announcements yet.');
    });

    it('pagination nav renders when links.length > 3', () => {
        const wrapper = mount(IndexPage, {
            global: globalOpts,
            props: {
                announcements: { data: [announcementFixture], links: paginationLinks },
            },
        });
        const nav = wrapper.find('nav[aria-label="Pagination"]');
        expect(nav.exists()).toBe(true);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mountWithPrimeVue(IndexPage, {
            props: { announcements: { data: [announcementFixture], links: [] } },
            global: { stubs },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
