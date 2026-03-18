import { describe, it, expect, vi, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// ---------------------------------------------------------------------------
// Mutable page-props object — tests mutate this before each mount so that
// the usePage() mock returns the right computed values from the watchEffect.
// ---------------------------------------------------------------------------
let mockPageProps: {
    auth: {
        user: { name: string; email: string; role: string };
        preferences: Record<string, unknown> | null;
    };
    features: Record<string, boolean>;
    flash: Record<string, string>;
} = {
    auth: {
        user: { name: 'Test User', email: 'test@example.com', role: 'student' },
        preferences: null,
    },
    features: {},
    flash: {},
};

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({ props: mockPageProps }),
    Link: { template: '<a href="#"><slot /></a>' },
    Head: { template: '<div />' },
}));

const mockRoute = vi.fn().mockImplementation((...args: unknown[]) => {
    if (args.length > 0) return `/${String(args[0])}`;
    return { current: vi.fn(() => false) };
});

vi.stubGlobal('route', mockRoute);

const stubs = {
    ApplicationLogo: true,
    NavLink: true,
    ResponsiveNavLink: true,
    Dropdown: {
        template: '<div><slot name="trigger" :open="false" /><slot name="content" /></div>',
    },
    DropdownLink: { template: '<a href="#"><slot /></a>' },
    Link: { template: '<a href="#"><slot /></a>' },
    Head: true,
};

const globalOpts = {
    stubs,
    mocks: {
        route: mockRoute,
        $page: {
            props: {
                auth: { user: { name: 'Test User', email: 'test@example.com', role: 'student' } },
                features: {},
                flash: {},
            },
        },
    },
};

afterEach(() => {
    document.documentElement.className = '';
    // Reset to safe defaults
    mockPageProps = {
        auth: {
            user: { name: 'Test User', email: 'test@example.com', role: 'student' },
            preferences: null,
        },
        features: {},
        flash: {},
    };
});

describe('AuthenticatedLayout — watchEffect class application', () => {
    it('applies high-contrast class when features["high-contrast"] is true', async () => {
        mockPageProps.features = { 'high-contrast': true };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('high-contrast')).toBe(true);
    });

    it('does not apply high-contrast when features["high-contrast"] is false', async () => {
        mockPageProps.features = { 'high-contrast': false };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('high-contrast')).toBe(false);
    });

    it('Pennant flag false overrides user preference true — no high-contrast class', async () => {
        mockPageProps.features = { 'high-contrast': false };
        mockPageProps.auth.preferences = { high_contrast: true };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('high-contrast')).toBe(false);
    });

    it('falls back to prefs.high_contrast when features object is absent', async () => {
        mockPageProps.features = {};
        mockPageProps.auth.preferences = { high_contrast: true };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('high-contrast')).toBe(true);
    });

    it('applies simplified class when features["simplified-layout"] is true', async () => {
        mockPageProps.features = { 'simplified-layout': true };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('simplified')).toBe(true);
    });

    it('applies reduce-motion class when prefs.reduced_motion is true', async () => {
        mockPageProps.auth.preferences = { reduced_motion: true };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('reduce-motion')).toBe(true);
    });

    it('applies font-large class when prefs.font_size is "large"', async () => {
        mockPageProps.auth.preferences = { font_size: 'large' };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('font-large')).toBe(true);
    });

    it('defaults to font-medium when prefs is null', async () => {
        mockPageProps.auth.preferences = null;
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('font-medium')).toBe(true);
    });

    it('removes stale font class when new font preference is applied', async () => {
        // Simulate a pre-existing stale font class on the html element
        document.documentElement.classList.add('font-small');
        mockPageProps.auth.preferences = { font_size: 'large' };
        mount(AuthenticatedLayout, { global: globalOpts });
        await nextTick();
        expect(document.documentElement.classList.contains('font-small')).toBe(false);
        expect(document.documentElement.classList.contains('font-large')).toBe(true);
    });
});
