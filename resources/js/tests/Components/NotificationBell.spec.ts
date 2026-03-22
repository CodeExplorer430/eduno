import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import NotificationBell from '@/Components/NotificationBell.vue';

// ---------------------------------------------------------------------------
// usePage mock — mutable so individual tests can override unread count.
// ---------------------------------------------------------------------------
let mockUnreadCount = 0;

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({
        props: {
            auth: {
                unread_notifications_count: mockUnreadCount,
            },
        },
    }),
    Link: {
        template: '<a v-bind="$attrs"><slot /></a>',
    },
}));

vi.stubGlobal('route', (name: string) => `/${name}`);

function mountBell(unreadCount = 0) {
    mockUnreadCount = unreadCount;
    return mount(NotificationBell, {
        global: {
            mocks: { route: (name: string) => `/${name}` },
        },
        attachTo: document.body,
    });
}

describe('NotificationBell', () => {
    it('renders a link to notifications index', () => {
        const wrapper = mountBell(0);
        const link = wrapper.find('a');
        expect(link.exists()).toBe(true);
        expect(link.attributes('href')).toBe('/notifications.index');
    });

    it('shows badge when unread_count > 0', () => {
        const wrapper = mountBell(3);
        const badge = wrapper.find('span[aria-hidden="true"]');
        expect(badge.exists()).toBe(true);
        expect(badge.text()).toBe('3');
    });

    it('hides badge when unread_count is 0', () => {
        const wrapper = mountBell(0);
        const badge = wrapper.find('span[aria-hidden="true"]');
        expect(badge.exists()).toBe(false);
    });

    it('shows 9+ when count > 9', () => {
        const wrapper = mountBell(15);
        const badge = wrapper.find('span[aria-hidden="true"]');
        expect(badge.exists()).toBe(true);
        expect(badge.text()).toBe('9+');
    });

    it('aria-label includes unread count when count > 0', () => {
        const wrapper = mountBell(5);
        const link = wrapper.find('a');
        expect(link.attributes('aria-label')).toContain('5');
    });

    it('aria-label is "Notifications" when count is 0', () => {
        const wrapper = mountBell(0);
        const link = wrapper.find('a');
        expect(link.attributes('aria-label')).toBe('Notifications');
    });

    it('has no axe WCAG violations', async () => {
        const wrapper = mountBell(2);
        expect(
            await axe(wrapper.element, { rules: { region: { enabled: false } } })
        ).toHaveNoViolations();
    });
});
