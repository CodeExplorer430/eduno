import { describe, it, expect, vi, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import NotificationsIndex from '@/Pages/Notifications/Index.vue';
import type { AppNotification, PaginatedResponse } from '@/Types/models';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<head></head>' },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        processing: false,
        post: vi.fn(),
    }),
}));

vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
    default: { template: '<div><slot name="header" /><slot /></div>' },
}));

vi.mock('@/Components/Pagination.vue', () => ({
    default: { template: '<nav aria-label="Pagination"></nav>' },
}));

beforeAll(() => {
    (globalThis as Record<string, unknown>).route = (name: string, id?: string) =>
        id ? `/${name}/${id}` : `/${name}`;
});

// ---------------------------------------------------------------------------
// Fixtures
// ---------------------------------------------------------------------------
const emptyNotifications: PaginatedResponse<AppNotification> = {
    data: [],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
};

const unreadNotification: AppNotification = {
    id: 'uuid-1',
    type: 'App\\Notifications\\AnnouncementPublishedNotification',
    data: { message: 'New announcement posted', url: '/announcements/1', type: 'announcement' },
    read_at: null,
    created_at: '2026-03-22T10:00:00Z',
    updated_at: '2026-03-22T10:00:00Z',
};

const readNotification: AppNotification = {
    id: 'uuid-2',
    type: 'App\\Notifications\\GradeReleasedNotification',
    data: { message: 'Your grade has been released', url: '/submissions/5', type: 'grade' },
    read_at: '2026-03-22T11:00:00Z',
    created_at: '2026-03-22T09:00:00Z',
    updated_at: '2026-03-22T11:00:00Z',
};

const oneUnreadResponse: PaginatedResponse<AppNotification> = {
    data: [unreadNotification],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
};

const oneReadResponse: PaginatedResponse<AppNotification> = {
    data: [readNotification],
    links: [],
    meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 },
};

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------
describe('Notifications/Index', () => {
    it('renders empty state when no notifications', () => {
        const wrapper = mount(NotificationsIndex, {
            props: { notifications: emptyNotifications, unread_count: 0 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
        });
        expect(wrapper.html()).toContain('You have no notifications yet');
    });

    it('renders unread notification with tinted row', () => {
        const wrapper = mount(NotificationsIndex, {
            props: { notifications: oneUnreadResponse, unread_count: 1 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
        });
        expect(wrapper.html()).toContain('New announcement posted');
        // unread row should have the indigo tint classes
        const li = wrapper.find('li');
        expect(li.classes()).toContain('bg-indigo-50');
    });

    it('renders read notification without tint', () => {
        const wrapper = mount(NotificationsIndex, {
            props: { notifications: oneReadResponse, unread_count: 0 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
        });
        expect(wrapper.html()).toContain('Your grade has been released');
        const li = wrapper.find('li');
        expect(li.classes()).toContain('bg-white');
        expect(li.classes()).not.toContain('bg-indigo-50');
    });

    it('shows "Mark all as read" button only when unread_count > 0', () => {
        const wrapperWithUnread = mount(NotificationsIndex, {
            props: { notifications: oneUnreadResponse, unread_count: 1 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
        });
        expect(wrapperWithUnread.html()).toContain('Mark all as read');

        const wrapperNoUnread = mount(NotificationsIndex, {
            props: { notifications: oneReadResponse, unread_count: 0 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
        });
        expect(wrapperNoUnread.html()).not.toContain('Mark all as read');
    });

    it('has no axe WCAG violations', async () => {
        const wrapper = mount(NotificationsIndex, {
            props: { notifications: emptyNotifications, unread_count: 0 },
            global: {
                mocks: {
                    route: (name: string, id?: string) => (id ? `/${name}/${id}` : `/${name}`),
                },
            },
            attachTo: document.body,
        });
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
