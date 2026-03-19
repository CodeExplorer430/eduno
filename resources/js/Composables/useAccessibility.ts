import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { UserPreferences } from '@/Types/models';

export function useAccessibility(): void {
    const page = usePage<PageProps<{ preferences: UserPreferences | null }>>();
    watch(
        () => page.props.preferences,
        (prefs) => {
            const el = document.documentElement;
            el.classList.toggle('font-small', prefs?.font_size === 'small');
            el.classList.toggle('font-large', prefs?.font_size === 'large');
            el.classList.toggle('high-contrast', prefs?.high_contrast === true);
            el.classList.toggle('reduced-motion', prefs?.reduced_motion === true);
            el.classList.toggle('simplified-layout', prefs?.simplified_layout === true);
        },
        { immediate: true }
    );
}
