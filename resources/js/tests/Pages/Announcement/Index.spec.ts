import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import AnnouncementIndex from '@/Pages/Announcement/Index.vue';
import type { Announcement, CourseSection, PaginatedResponse } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href', 'method', 'as'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        post: vi.fn(),
    }),
}));

vi.mock('@/Components/Pagination.vue', () => ({
    default: { template: '<nav aria-label="Pagination"></nav>' },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string) => `/${name}`;
});

const section: CourseSection = {
    id: 1,
    section_name: 'CS101-A',
    block_code: null,
    course_id: 1,
    instructor_id: 1,
    schedule_text: null,
    created_at: '2026-01-01T00:00:00Z',
    updated_at: '2026-01-01T00:00:00Z',
};

const emptyAnnouncements: PaginatedResponse<Announcement> = {
    data: [],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
};

const sampleAnnouncements: PaginatedResponse<Announcement> = {
    data: [
        {
            id: 1,
            course_section_id: 1,
            title: 'Welcome to the course!',
            body: 'Hello students.',
            published_at: '2026-03-01T00:00:00Z',
            created_by: 2,
            author: {
                id: 2,
                name: 'Prof. Santos',
                email: 'santos@ucc.edu.ph',
                role: 'instructor',
                email_verified_at: null,
                created_at: '2026-01-01T00:00:00Z',
                updated_at: '2026-01-01T00:00:00Z',
            },
            created_at: '2026-03-01T00:00:00Z',
            updated_at: '2026-03-01T00:00:00Z',
        },
    ],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
};

describe('Announcement/Index', () => {
    it('renders the "Announcements" heading', () => {
        const wrapper = mount(AnnouncementIndex, {
            props: { section, announcements: emptyAnnouncements, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Announcements');
    });

    it('renders empty state when no announcements', () => {
        const wrapper = mount(AnnouncementIndex, {
            props: { section, announcements: emptyAnnouncements, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('No announcements yet');
    });

    it('renders announcement title when data is present', () => {
        const wrapper = mount(AnnouncementIndex, {
            props: { section, announcements: sampleAnnouncements, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('Welcome to the course!');
    });

    it('renders "New Announcement" link when canManage is true', () => {
        const wrapper = mount(AnnouncementIndex, {
            props: { section, announcements: emptyAnnouncements, canManage: true },
            global: { mocks: { route: (name: string) => `/${name}` } },
        });
        expect(wrapper.html()).toContain('New Announcement');
    });

    it('has no axe violations', async () => {
        const wrapper = mount(AnnouncementIndex, {
            props: { section, announcements: emptyAnnouncements, canManage: false },
            global: { mocks: { route: (name: string) => `/${name}` } },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
