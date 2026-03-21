<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ToggleSwitch from 'primevue/toggleswitch';
import Select from 'primevue/select';
import Button from 'primevue/button';
import type { UserPreferences } from '@/Types/models';

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
    language: props.preferences?.language ?? 'en',
});

const successMessage = computed(
    () => (usePage().props.flash as Record<string, string> | undefined)?.success ?? null
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

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <form
                        aria-label="Accessibility preferences form"
                        novalidate
                        @submit.prevent="submit"
                    >
                        <div class="space-y-6">
                            <!-- Font Size -->
                            <div>
                                <InputLabel for="font_size" value="Font Size" />
                                <p id="font_size_desc" class="mt-1 text-sm text-gray-500">
                                    Choose the base text size across the application.
                                </p>
                                <Select
                                    id="font_size"
                                    v-model="form.font_size"
                                    :options="fontSizeOptions"
                                    option-label="label"
                                    option-value="value"
                                    class="mt-2 w-full"
                                    aria-describedby="font_size_desc"
                                />
                                <InputError class="mt-1" :message="form.errors.font_size" />
                            </div>

                            <!-- Language -->
                            <div>
                                <InputLabel for="language" value="Language" />
                                <p id="language_desc" class="mt-1 text-sm text-gray-500">
                                    Select your preferred interface language.
                                </p>
                                <Select
                                    id="language"
                                    v-model="form.language"
                                    :options="languageOptions"
                                    option-label="label"
                                    option-value="value"
                                    class="mt-2 w-full"
                                    aria-describedby="language_desc"
                                />
                                <InputError class="mt-1" :message="form.errors.language" />
                            </div>

                            <!-- High Contrast -->
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <ToggleSwitch
                                        v-model="form.high_contrast"
                                        input-id="high_contrast"
                                        aria-describedby="high_contrast_desc"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="high_contrast" value="High Contrast" />
                                    <p id="high_contrast_desc" class="mt-1 text-sm text-gray-500">
                                        Increases contrast ratio to improve readability.
                                    </p>
                                    <InputError class="mt-1" :message="form.errors.high_contrast" />
                                </div>
                            </div>

                            <!-- Reduced Motion -->
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <ToggleSwitch
                                        v-model="form.reduced_motion"
                                        input-id="reduced_motion"
                                        aria-describedby="reduced_motion_desc"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="reduced_motion" value="Reduce Motion" />
                                    <p id="reduced_motion_desc" class="mt-1 text-sm text-gray-500">
                                        Disables animations and transitions throughout the
                                        application.
                                    </p>
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.reduced_motion"
                                    />
                                </div>
                            </div>

                            <!-- Simplified Layout -->
                            <div class="flex items-start gap-4">
                                <div class="pt-1">
                                    <ToggleSwitch
                                        v-model="form.simplified_layout"
                                        input-id="simplified_layout"
                                        aria-describedby="simplified_layout_desc"
                                    />
                                </div>
                                <div>
                                    <InputLabel for="simplified_layout" value="Simplified Layout" />
                                    <p
                                        id="simplified_layout_desc"
                                        class="mt-1 text-sm text-gray-500"
                                    >
                                        Hides decorative elements to reduce visual complexity.
                                    </p>
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.simplified_layout"
                                    />
                                </div>
                            </div>

                            <!-- Live Preview -->
                            <section
                                aria-labelledby="preview-heading"
                                class="rounded-lg border border-gray-200 p-4"
                            >
                                <h2
                                    id="preview-heading"
                                    class="mb-2 text-sm font-medium text-gray-700"
                                >
                                    Preview
                                </h2>
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
                            </section>

                            <!-- Success message -->
                            <div
                                v-if="successMessage"
                                role="status"
                                aria-live="polite"
                                class="rounded-md bg-green-50 p-3 text-sm text-green-700"
                            >
                                {{ successMessage }}
                            </div>

                            <div class="flex items-center gap-4">
                                <Button
                                    type="submit"
                                    label="Save Preferences"
                                    :disabled="form.processing"
                                />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
