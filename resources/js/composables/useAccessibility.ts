import { computed, type ComputedRef } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { UserPreferences } from '@/Types/models';

interface AuthUser {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    preferences?: UserPreferences;
}

type AccessibilityPageProps = PageProps<{
    auth: { user: AuthUser };
}>;

interface AccessibilityReturn {
    preferences: ComputedRef<UserPreferences | undefined>;
    fontSizeClass: ComputedRef<string>;
    isHighContrast: ComputedRef<boolean>;
    isReducedMotion: ComputedRef<boolean>;
    isSimplifiedLayout: ComputedRef<boolean>;
}

export function useAccessibility(): AccessibilityReturn {
    const page = usePage<AccessibilityPageProps>();

    const preferences = computed(() => page.props.auth?.user?.preferences);

    const fontSizeClass = computed(() => {
        switch (preferences.value?.font_size) {
            case 'small':
                return 'text-sm';
            case 'large':
                return 'text-lg';
            default:
                return 'text-base';
        }
    });

    const isHighContrast = computed(() => preferences.value?.high_contrast ?? false);

    const isReducedMotion = computed(() => preferences.value?.reduced_motion ?? false);

    const isSimplifiedLayout = computed(() => preferences.value?.simplified_layout ?? false);

    return {
        preferences,
        fontSizeClass,
        isHighContrast,
        isReducedMotion,
        isSimplifiedLayout,
    };
}
