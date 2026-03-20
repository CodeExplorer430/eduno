import { describe, it, expect, vi, beforeEach } from 'vitest';

// Must mock before importing composable
vi.mock('@inertiajs/vue3', () => {
    const patchMock = vi.fn();
    return {
        usePage: vi.fn(() => ({
            props: {
                userPrefs: {
                    dyslexia_font: false,
                    reduced_motion: false,
                    high_contrast: false,
                    font_size: 'medium',
                },
            },
        })),
        useForm: vi.fn(() => ({
            reduced_motion: false,
            high_contrast: false,
            dyslexia_font: false,
            font_size: 'medium',
            patch: patchMock,
        })),
    };
});

// mock ziggy route
vi.stubGlobal('route', () => '/profile/preferences');

import { useA11yPrefs } from '@/composables/useA11yPrefs';

describe('useA11yPrefs', () => {
    beforeEach(() => {
        document.documentElement.className = '';
        delete (document.documentElement.dataset as Record<string, string | undefined>).fontSize;
    });

    it('toggles dyslexia-font class when dyslexia_font pref is true', async () => {
        const { usePage } = await import('@inertiajs/vue3');
        (usePage as ReturnType<typeof vi.fn>).mockReturnValueOnce({
            props: {
                userPrefs: {
                    dyslexia_font: true,
                    reduced_motion: false,
                    high_contrast: false,
                    font_size: 'medium',
                },
            },
        });

        useA11yPrefs();

        expect(document.documentElement.classList.contains('dyslexia-font')).toBe(true);
    });

    it('sets data-fontSize attribute on documentElement', async () => {
        const { usePage } = await import('@inertiajs/vue3');
        (usePage as ReturnType<typeof vi.fn>).mockReturnValueOnce({
            props: {
                userPrefs: {
                    dyslexia_font: false,
                    reduced_motion: false,
                    high_contrast: false,
                    font_size: 'large',
                },
            },
        });

        useA11yPrefs();

        expect(document.documentElement.dataset.fontSize).toBe('large');
    });

    it('updatePref calls form.patch', async () => {
        const { useForm } = await import('@inertiajs/vue3');
        const patchMock = vi.fn();
        (useForm as ReturnType<typeof vi.fn>).mockReturnValueOnce({
            reduced_motion: false,
            high_contrast: false,
            dyslexia_font: false,
            font_size: 'medium',
            patch: patchMock,
        });

        const { updatePref } = useA11yPrefs();
        updatePref('dyslexia_font', true);

        expect(patchMock).toHaveBeenCalled();
    });
});
