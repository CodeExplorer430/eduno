import { watch } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import type { PageProps } from '@/types';

export interface A11yPrefs {
    reduced_motion?: boolean;
    high_contrast?: boolean;
    dyslexia_font?: boolean;
    font_size?: string;
}

type A11yPageProps = PageProps<{ userPrefs?: A11yPrefs }>;

export function useA11yPrefs(): {
    prefs: A11yPrefs | undefined;
    updatePref: (key: keyof A11yPrefs, value: boolean | string) => void;
} {
    const page = usePage<A11yPageProps>();

    const form = useForm<A11yPrefs>({
        reduced_motion: false,
        high_contrast: false,
        dyslexia_font: false,
        font_size: 'medium',
    });

    function applyPrefs(prefs: A11yPrefs | undefined): void {
        const html = document.documentElement;

        html.classList.toggle('dyslexia-font', prefs?.dyslexia_font === true);
        html.classList.toggle('reduce-motion', prefs?.reduced_motion === true);
        html.classList.toggle('high-contrast', prefs?.high_contrast === true);

        if (prefs?.font_size) {
            html.dataset.fontSize = prefs.font_size;
        }
    }

    // Apply on initial load
    applyPrefs(page.props?.userPrefs);

    // Re-apply whenever the shared prop changes (e.g. after a save)
    watch(
        () => page.props?.userPrefs,
        (prefs) => applyPrefs(prefs),
        { deep: true }
    );

    function updatePref(key: keyof A11yPrefs, value: boolean | string): void {
        (form[key] as boolean | string) = value;
        form.patch(route('profile.preferences'), { preserveScroll: true });
    }

    return {
        prefs: page.props?.userPrefs,
        updatePref,
    };
}
