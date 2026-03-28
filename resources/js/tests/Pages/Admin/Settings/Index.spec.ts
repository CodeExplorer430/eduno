import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import SettingsPage from '@/Pages/Admin/Settings/Index.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: vi.fn(() => ({
        site_name: 'Eduno',
        registration_open: 'true',
        maintenance_mode: 'false',
        email_notifications: 'true',
        deadline_reminder_hours: 24,
        email_digest: 'false',
        patch: vi.fn(),
        processing: false,
        errors: {},
        wasSuccessful: false,
    })),
}));

const stubs = {
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
};

const routeMock = vi.fn(() => '/');
const globalOpts = { stubs, mocks: { route: routeMock } };

const props = {
    settings: {
        general: {
            site_name: 'Eduno',
            registration_open: 'true',
            maintenance_mode: 'false',
        },
        notifications: {
            email_notifications: 'true',
            deadline_reminder_hours: '24',
            email_digest: 'false',
        },
    },
};

describe('Admin/Settings/Index', () => {
    it('renders without crashing', () => {
        const wrapper = mount(SettingsPage, { props, global: globalOpts });
        expect(wrapper.exists()).toBe(true);
    });

    it('General tab is visible by default', () => {
        const wrapper = mount(SettingsPage, { props, global: globalOpts });
        expect(wrapper.text()).toContain('General Settings');
    });

    it('settings form has accessible tab interface with role="tablist"', () => {
        const wrapper = mount(SettingsPage, { props, global: globalOpts });
        expect(wrapper.find('[role="tablist"]').exists()).toBe(true);
    });

    it('tab buttons have role="tab"', () => {
        const wrapper = mount(SettingsPage, { props, global: globalOpts });
        const tabs = wrapper.findAll('[role="tab"]');
        expect(tabs.length).toBeGreaterThan(0);
    });

    it('passes WCAG axe check', async () => {
        const wrapper = mount(SettingsPage, { props, global: globalOpts });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
    });
});
