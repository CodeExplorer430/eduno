<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import {
    MagnifyingGlassIcon,
    BoltSlashIcon,
    ViewColumnsIcon,
    LanguageIcon,
    MoonIcon,
} from '@heroicons/vue/24/outline';
import type { UserPreferences } from '@/Types/models';
import { useAppToast } from '@/composables/useAppToast';

const props = defineProps<{
    preferences: UserPreferences | null;
}>();

const fontSizeOptions = [
    { label: 'Small', value: 'small' },
    { label: 'Medium', value: 'medium' },
    { label: 'Large', value: 'large' },
    { label: 'X-Large', value: 'xlarge' },
];

const languageOptions = [{ label: 'English', value: 'en' }];

const form = useForm({
    font_size: props.preferences?.font_size ?? 'medium',
    high_contrast: props.preferences?.high_contrast ?? false,
    reduced_motion: props.preferences?.reduced_motion ?? false,
    simplified_layout: props.preferences?.simplified_layout ?? false,
    dark_mode: props.preferences?.dark_mode ?? false,
    language: props.preferences?.language ?? 'en',
});

const appToast = useAppToast();

watch(
    () => form.wasSuccessful,
    (val) => {
        if (val) appToast.success('Preferences saved.');
    }
);

function submit(): void {
    form.patch(route('profile.accessibility.update'));
}
</script>

<template>
    <Head title="Accessibility Preferences" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">
                Accessibility Preferences
            </h1>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <form aria-label="Accessibility preferences form" novalidate @submit.prevent="submit">
                <div class="space-y-4">
                    <!-- Font Size -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50"
                            >
                                <MagnifyingGlassIcon
                                    class="h-5 w-5 text-blue-600"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="flex-1">
                                <h2 class="font-semibold text-gray-900">Font Size</h2>
                                <p id="font_size_desc" class="text-sm text-gray-500">
                                    Choose the base text size across the application.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <div
                                class="flex flex-wrap gap-2"
                                role="group"
                                aria-label="Font size options"
                                aria-describedby="font_size_desc"
                            >
                                <label
                                    v-for="opt in fontSizeOptions"
                                    :key="opt.value"
                                    class="cursor-pointer"
                                >
                                    <input
                                        v-model="form.font_size"
                                        type="radio"
                                        name="font_size"
                                        :value="opt.value"
                                        class="sr-only"
                                    />
                                    <span
                                        class="inline-flex items-center rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                                        :class="
                                            form.font_size === opt.value
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'
                                        "
                                    >
                                        {{ opt.label }}
                                    </span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.font_size" />

                            <!-- Live Preview -->
                            <div
                                class="mt-4 rounded-lg border border-gray-100 bg-gray-50 px-4 py-3"
                            >
                                <p
                                    class="mb-1 text-xs font-semibold uppercase tracking-wider text-gray-400"
                                >
                                    Preview
                                </p>
                                <p
                                    :style="{
                                        fontSize: {
                                            small: '14px',
                                            medium: '16px',
                                            large: '18px',
                                            xlarge: '20px',
                                        }[form.font_size],
                                    }"
                                    class="text-gray-800"
                                >
                                    The quick brown fox jumps over the lazy dog.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- High Contrast -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 px-6 py-5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-50"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-purple-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label
                                    for="high_contrast"
                                    class="block font-semibold text-gray-900 cursor-pointer"
                                >
                                    High Contrast
                                </label>
                                <p id="high_contrast_desc" class="text-sm text-gray-500">
                                    Increases contrast ratio to improve readability.
                                </p>
                                <InputError class="mt-1" :message="form.errors.high_contrast" />
                            </div>
                            <button
                                id="high_contrast"
                                type="button"
                                :aria-pressed="form.high_contrast"
                                :aria-describedby="'high_contrast_desc'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-gray-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="form.high_contrast ? 'bg-blue-600' : 'bg-gray-300'"
                                @click="form.high_contrast = !form.high_contrast"
                            >
                                <span class="sr-only">Toggle high contrast</span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="form.high_contrast ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Reduced Motion -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 px-6 py-5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-50"
                            >
                                <BoltSlashIcon class="h-5 w-5 text-amber-600" aria-hidden="true" />
                            </div>
                            <div class="flex-1">
                                <label
                                    for="reduced_motion"
                                    class="block font-semibold text-gray-900 cursor-pointer"
                                >
                                    Reduce Motion
                                </label>
                                <p id="reduced_motion_desc" class="text-sm text-gray-500">
                                    Disables animations and transitions throughout the application.
                                </p>
                                <InputError class="mt-1" :message="form.errors.reduced_motion" />
                            </div>
                            <button
                                id="reduced_motion"
                                type="button"
                                :aria-pressed="form.reduced_motion"
                                :aria-describedby="'reduced_motion_desc'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-gray-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="form.reduced_motion ? 'bg-blue-600' : 'bg-gray-300'"
                                @click="form.reduced_motion = !form.reduced_motion"
                            >
                                <span class="sr-only">Toggle reduce motion</span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="form.reduced_motion ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Simplified Layout -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 px-6 py-5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-50"
                            >
                                <ViewColumnsIcon
                                    class="h-5 w-5 text-green-600"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="flex-1">
                                <label
                                    for="simplified_layout"
                                    class="block font-semibold text-gray-900 cursor-pointer"
                                >
                                    Simplified Layout
                                </label>
                                <p id="simplified_layout_desc" class="text-sm text-gray-500">
                                    Hides decorative elements to reduce visual complexity.
                                </p>
                                <InputError class="mt-1" :message="form.errors.simplified_layout" />
                            </div>
                            <button
                                id="simplified_layout"
                                type="button"
                                :aria-pressed="form.simplified_layout"
                                :aria-describedby="'simplified_layout_desc'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-gray-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="form.simplified_layout ? 'bg-blue-600' : 'bg-gray-300'"
                                @click="form.simplified_layout = !form.simplified_layout"
                            >
                                <span class="sr-only">Toggle simplified layout</span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="
                                        form.simplified_layout ? 'translate-x-5' : 'translate-x-0'
                                    "
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Dark Mode -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 px-6 py-5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-50"
                            >
                                <MoonIcon class="h-5 w-5 text-slate-600" aria-hidden="true" />
                            </div>
                            <div class="flex-1">
                                <label
                                    for="dark_mode"
                                    class="block cursor-pointer font-semibold text-gray-900"
                                >
                                    Dark Mode
                                </label>
                                <p id="dark_mode_desc" class="text-sm text-gray-500">
                                    Switches the interface to a dark colour scheme.
                                </p>
                                <InputError class="mt-1" :message="form.errors.dark_mode" />
                            </div>
                            <button
                                id="dark_mode"
                                type="button"
                                :aria-pressed="form.dark_mode"
                                :aria-describedby="'dark_mode_desc'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-gray-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="form.dark_mode ? 'bg-blue-600' : 'bg-gray-300'"
                                @click="form.dark_mode = !form.dark_mode"
                            >
                                <span class="sr-only">Toggle dark mode</span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="form.dark_mode ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50"
                            >
                                <LanguageIcon class="h-5 w-5 text-indigo-600" aria-hidden="true" />
                            </div>
                            <div class="flex-1">
                                <h2 class="font-semibold text-gray-900">Language</h2>
                                <p id="language_desc" class="text-sm text-gray-500">
                                    Select your preferred interface language.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <div
                                class="flex flex-wrap gap-2"
                                role="group"
                                aria-label="Language options"
                                aria-describedby="language_desc"
                            >
                                <label
                                    v-for="opt in languageOptions"
                                    :key="opt.value"
                                    class="cursor-pointer"
                                >
                                    <input
                                        v-model="form.language"
                                        type="radio"
                                        name="language"
                                        :value="opt.value"
                                        class="sr-only"
                                    />
                                    <span
                                        class="inline-flex items-center rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                                        :class="
                                            form.language === opt.value
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'
                                        "
                                    >
                                        {{ opt.label }}
                                    </span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.language" />
                        </div>
                    </div>
                </div>

                <!-- Save button -->
                <div class="mt-6 flex items-center justify-end gap-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-60"
                    >
                        {{ form.processing ? 'Saving…' : 'Save Preferences' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
