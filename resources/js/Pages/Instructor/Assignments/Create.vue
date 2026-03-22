<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Section {
    id: number;
    section_name: string;
    course?: { title: string };
}

const props = defineProps<{ section: Section }>();

interface FileTypeOption {
    label: string;
    mime: string;
}

const FILE_TYPE_OPTIONS: FileTypeOption[] = [
    { label: 'PDF', mime: 'application/pdf' },
    {
        label: 'Word (.docx)',
        mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    },
    {
        label: 'PowerPoint (.pptx)',
        mime: 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    },
    { label: 'ZIP', mime: 'application/zip' },
    { label: 'Plain Text (.txt)', mime: 'text/plain' },
    { label: 'Image (PNG/JPG)', mime: 'image/png' },
    {
        label: 'Excel (.xlsx)',
        mime: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    },
];

const form = useForm<{
    title: string;
    instructions: string;
    due_at: string;
    max_score: string;
    allow_resubmission: boolean;
    allowed_file_types: string[];
}>({
    title: '',
    instructions: '',
    due_at: '',
    max_score: '100',
    allow_resubmission: false,
    allowed_file_types: [],
});

const submit = (): void => {
    form.post(route('instructor.courses.assignments.store', { course: props.section.id }));
};
</script>

<template>
    <Head title="Create Assignment" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Create Assignment</li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <section
                aria-labelledby="create-assignment-heading"
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
            >
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <div>
                        <h1 id="create-assignment-heading" class="font-semibold text-gray-900">
                            Create Assignment
                        </h1>
                        <p class="text-xs text-gray-500">
                            {{ section.course?.title }} — {{ section.section_name }}
                        </p>
                    </div>
                </div>

                <form novalidate @submit.prevent="submit">
                    <div class="space-y-5 px-6 py-6">
                        <div>
                            <InputLabel for="assignment-title">
                                Title
                                <span class="text-red-500" aria-hidden="true">*</span>
                                <span class="sr-only">(required)</span>
                            </InputLabel>
                            <input
                                id="assignment-title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-describedby="assignment-title-error"
                                :aria-invalid="!!form.errors.title"
                                required
                                autofocus
                            />
                            <InputError
                                id="assignment-title-error"
                                class="mt-1"
                                :message="form.errors.title"
                            />
                        </div>

                        <div>
                            <InputLabel for="assignment-instructions">Instructions</InputLabel>
                            <textarea
                                id="assignment-instructions"
                                v-model="form.instructions"
                                rows="5"
                                aria-describedby="assignment-instructions-error"
                                :aria-invalid="!!form.errors.instructions"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Describe what students need to do…"
                            ></textarea>
                            <InputError
                                id="assignment-instructions-error"
                                class="mt-1"
                                :message="form.errors.instructions"
                            />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="assignment-due-at">Due Date</InputLabel>
                                <input
                                    id="assignment-due-at"
                                    v-model="form.due_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    aria-describedby="assignment-due-at-error"
                                    :aria-invalid="!!form.errors.due_at"
                                />
                                <InputError
                                    id="assignment-due-at-error"
                                    class="mt-1"
                                    :message="form.errors.due_at"
                                />
                            </div>
                            <div>
                                <InputLabel for="assignment-max-score">
                                    Max Score
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="assignment-max-score"
                                    v-model="form.max_score"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    aria-describedby="assignment-max-score-error"
                                    :aria-invalid="!!form.errors.max_score"
                                    required
                                />
                                <InputError
                                    id="assignment-max-score-error"
                                    class="mt-1"
                                    :message="form.errors.max_score"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="assignment-allow-resubmission"
                                v-model="form.allow_resubmission"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <label
                                for="assignment-allow-resubmission"
                                class="text-sm text-gray-700"
                            >
                                Allow resubmission
                            </label>
                        </div>

                        <fieldset>
                            <legend class="mb-2 block text-sm font-medium text-gray-700">
                                Accepted File Types
                                <span class="ml-1 font-normal text-gray-500"
                                    >(leave all unchecked to accept any type)</span
                                >
                            </legend>
                            <div
                                class="flex flex-wrap gap-2"
                                aria-describedby="allowed-file-types-error"
                            >
                                <label
                                    v-for="option in FILE_TYPE_OPTIONS"
                                    :key="option.mime"
                                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1 text-sm transition-colors"
                                    :class="
                                        form.allowed_file_types.includes(option.mime)
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                                    "
                                >
                                    <input
                                        :id="`file-type-${option.mime}`"
                                        v-model="form.allowed_file_types"
                                        type="checkbox"
                                        :value="option.mime"
                                        class="sr-only"
                                    />
                                    {{ option.label }}
                                </label>
                            </div>
                            <InputError
                                id="allowed-file-types-error"
                                class="mt-1"
                                :message="form.errors.allowed_file_types"
                            />
                        </fieldset>
                    </div>

                    <div
                        v-if="form.hasErrors && Object.keys(form.errors).length > 0"
                        role="alert"
                        aria-live="assertive"
                        class="mx-6 mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        Please fix the errors above before submitting.
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                    >
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :aria-busy="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                        >
                            <span v-if="form.processing">Creating&hellip;</span>
                            <span v-else>Create Assignment</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
