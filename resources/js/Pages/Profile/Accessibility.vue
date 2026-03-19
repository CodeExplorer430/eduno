<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { UserPreferences } from '@/Types/models';

const props = defineProps<{
    preferences: UserPreferences | null;
}>();

const form = useForm({
    font_size: props.preferences?.font_size ?? 'medium',
    high_contrast: props.preferences?.high_contrast ?? false,
    reduced_motion: props.preferences?.reduced_motion ?? false,
    simplified_layout: props.preferences?.simplified_layout ?? false,
});

const page = usePage<PageProps<{ status?: string }>>();
const saved = computed(() => page.props.status === 'preferences-updated');

function submit(): void {
    form.put(route('preferences.update'));
}
</script>

<template>
    <Head title="Accessibility Settings" />

    <main class="mx-auto max-w-2xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
                <li>
                    <Link
                        :href="route('profile.edit')"
                        class="hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                    >
                        Profile
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Accessibility Settings</li>
            </ol>
        </nav>

        <h1 class="mb-6 text-2xl font-bold">Accessibility Settings</h1>

        <div
            v-if="saved"
            class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-green-800"
            role="status"
            aria-live="polite"
        >
            Preferences saved successfully.
        </div>

        <form novalidate @submit.prevent="submit">
            <fieldset class="mb-6">
                <legend class="mb-2 text-base font-medium text-gray-900">Font Size</legend>

                <div class="space-y-2">
                    <div
                        v-for="size in ['small', 'medium', 'large'] as const"
                        :key="size"
                        class="flex items-center gap-2"
                    >
                        <input
                            :id="`font-size-${size}`"
                            v-model="form.font_size"
                            type="radio"
                            :value="size"
                            name="font_size"
                            class="h-4 w-4 accent-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            :aria-describedby="
                                form.errors.font_size ? 'font-size-error' : undefined
                            "
                        />
                        <label :for="`font-size-${size}`" class="capitalize text-gray-700">{{
                            size
                        }}</label>
                    </div>
                </div>

                <p
                    v-if="form.errors.font_size"
                    id="font-size-error"
                    class="mt-1 text-sm text-red-600"
                    role="alert"
                >
                    {{ form.errors.font_size }}
                </p>
            </fieldset>

            <fieldset class="mb-6">
                <legend class="mb-2 text-base font-medium text-gray-900">Display Options</legend>

                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <input
                            id="high-contrast"
                            v-model="form.high_contrast"
                            type="checkbox"
                            class="h-4 w-4 accent-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            :aria-describedby="
                                form.errors.high_contrast ? 'high-contrast-error' : undefined
                            "
                        />
                        <label for="high-contrast" class="text-gray-700">High contrast mode</label>
                        <p
                            v-if="form.errors.high_contrast"
                            id="high-contrast-error"
                            class="text-sm text-red-600"
                            role="alert"
                        >
                            {{ form.errors.high_contrast }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="reduced-motion"
                            v-model="form.reduced_motion"
                            type="checkbox"
                            class="h-4 w-4 accent-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            :aria-describedby="
                                form.errors.reduced_motion ? 'reduced-motion-error' : undefined
                            "
                        />
                        <label for="reduced-motion" class="text-gray-700">Reduce motion</label>
                        <p
                            v-if="form.errors.reduced_motion"
                            id="reduced-motion-error"
                            class="text-sm text-red-600"
                            role="alert"
                        >
                            {{ form.errors.reduced_motion }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="simplified-layout"
                            v-model="form.simplified_layout"
                            type="checkbox"
                            class="h-4 w-4 accent-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            :aria-describedby="
                                form.errors.simplified_layout
                                    ? 'simplified-layout-error'
                                    : undefined
                            "
                        />
                        <label for="simplified-layout" class="text-gray-700"
                            >Simplified layout</label
                        >
                        <p
                            v-if="form.errors.simplified_layout"
                            id="simplified-layout-error"
                            class="text-sm text-red-600"
                            role="alert"
                        >
                            {{ form.errors.simplified_layout }}
                        </p>
                    </div>
                </div>
            </fieldset>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50"
            >
                Save Preferences
            </button>
        </form>
    </main>
</template>
