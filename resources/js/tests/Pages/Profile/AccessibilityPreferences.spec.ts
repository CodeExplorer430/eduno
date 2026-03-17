import { describe, it, expect, vi } from 'vitest';
import AccessibilityPage from '@/Pages/Profile/Accessibility.vue';
import { mountWithPrimeVue } from '@/tests/helpers';

const mockRoute = vi.fn((name: string): string => `/${name}`);
const mockUseForm = vi.fn(
    (data: Record<string, unknown>): Record<string, unknown> => ({
        ...data,
        errors: {},
        processing: false,
        patch: vi.fn(),
    })
);

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div />' },
    useForm: (data: Record<string, unknown>) => mockUseForm(data),
    usePage: () => ({
        props: {
            auth: { user: { name: 'Test', email: 'test@example.com', role: 'student' } },
            flash: {},
        },
    }),
}));

vi.stubGlobal('route', mockRoute);

const stubs = {
    Head: true,
    AuthenticatedLayout: { template: '<div><slot /><slot name="header" /></div>' },
    Select: {
        template: '<select :id="inputId"><slot /></select>',
        props: ['inputId', 'modelValue', 'options', 'optionLabel', 'optionValue', 'placeholder'],
    },
    ToggleSwitch: {
        template: '<input type="checkbox" :id="inputId" />',
        props: ['inputId', 'modelValue'],
    },
    Button: {
        template: '<button type="submit"><slot /></button>',
        props: ['label', 'disabled', 'tag', 'href', 'severity', 'size'],
    },
    InputLabel: {
        template: '<label :for="forInput">{{ value }}</label>',
        props: ['for', 'forInput', 'value'],
    },
    InputError: { template: '<span>{{ message }}</span>', props: ['message'] },
};

describe('Profile/Accessibility', () => {
    it('renders without crashing', () => {
        const wrapper = mountWithPrimeVue(AccessibilityPage, {
            props: { preferences: null },
            global: { stubs },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('renders the font size selector', () => {
        const wrapper = mountWithPrimeVue(AccessibilityPage, {
            props: { preferences: null },
            global: { stubs },
        });
        expect(wrapper.html()).toContain('font_size');
    });

    it('renders the high contrast toggle', () => {
        const wrapper = mountWithPrimeVue(AccessibilityPage, {
            props: { preferences: null },
            global: { stubs },
        });
        expect(wrapper.html()).toContain('high_contrast');
    });

    it('renders the reduced motion toggle', () => {
        const wrapper = mountWithPrimeVue(AccessibilityPage, {
            props: { preferences: null },
            global: { stubs },
        });
        expect(wrapper.html()).toContain('reduced_motion');
    });

    it('renders the simplified layout toggle', () => {
        const wrapper = mountWithPrimeVue(AccessibilityPage, {
            props: { preferences: null },
            global: { stubs },
        });
        expect(wrapper.html()).toContain('simplified_layout');
    });

    it('populates form with existing preferences', () => {
        mockUseForm.mockImplementationOnce(
            (data: Record<string, unknown>): Record<string, unknown> => ({
                ...data,
                errors: {},
                processing: false,
                patch: vi.fn(),
            })
        );

        mountWithPrimeVue(AccessibilityPage, {
            props: {
                preferences: {
                    id: 1,
                    user_id: 1,
                    font_size: 'large',
                    high_contrast: true,
                    reduced_motion: false,
                    simplified_layout: true,
                    language: 'en',
                },
            },
            global: { stubs },
        });

        expect(mockUseForm).toHaveBeenCalledWith(
            expect.objectContaining({ font_size: 'large', high_contrast: true })
        );
    });
});
