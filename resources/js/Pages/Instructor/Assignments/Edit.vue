<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface FileTypeOption {
    label: string;
    mimes: string[];
}

const FILE_TYPE_OPTIONS: FileTypeOption[] = [
    { label: 'PDF', mimes: ['application/pdf'] },
    {
        label: 'Word (.doc/.docx)',
        mimes: [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
    },
    {
        label: 'PowerPoint (.ppt/.pptx)',
        mimes: [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
    },
    { label: 'ZIP', mimes: ['application/zip'] },
    { label: 'Plain Text (.txt)', mimes: ['text/plain'] },
    { label: 'Image (PNG/JPG)', mimes: ['image/png', 'image/jpeg'] },
    {
        label: 'Excel (.xls/.xlsx)',
        mimes: [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
    },
];

function isSelected(option: FileTypeOption): boolean {
    return option.mimes.some((m) => form.allowed_file_types.includes(m));
}

function toggleOption(option: FileTypeOption): void {
    const allSelected = option.mimes.every((m) => form.allowed_file_types.includes(m));
    if (allSelected) {
        form.allowed_file_types = form.allowed_file_types.filter((m) => !option.mimes.includes(m));
    } else {
        for (const m of option.mimes) {
            if (!form.allowed_file_types.includes(m)) {
                form.allowed_file_types.push(m);
            }
        }
    }
}

interface Props {
    section: { id: number; section_name: string };
    assignment: {
        id: number;
        title: string;
        instructions: string | null;
        due_at: string | null;
        max_score: number;
        allow_resubmission: boolean;
        allowed_file_types: string[] | null;
    };
}

const props = defineProps<Props>();

const form = useForm<{
    title: string;
    instructions: string;
    due_at: string;
    max_score: string;
    allow_resubmission: boolean;
    allowed_file_types: string[];
}>({
    title: props.assignment.title,
    instructions: props.assignment.instructions ?? '',
    due_at: props.assignment.due_at
        ? new Date(props.assignment.due_at).toISOString().slice(0, 16)
        : '',
    max_score: String(props.assignment.max_score),
    allow_resubmission: props.assignment.allow_resubmission,
    allowed_file_types: props.assignment.allowed_file_types ?? [],
});

const submit = (): void => {
    form.patch(route('instructor.assignments.update', props.assignment.id));
};
</script>

<template>
    <Head title="Edit Assignment" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                    <li>
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-gray-200"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800 dark:text-gray-200" aria-current="page">
                        Edit Assignment
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <section
                aria-labelledby="edit-assignment-heading"
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <div>
                        <h1
                            id="edit-assignment-heading"
                            class="font-semibold text-gray-900 dark:text-white"
                        >
                            Edit Assignment
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-slate-400">
                            Section: {{ section.section_name }}
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
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
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
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
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
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
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
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
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
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800"
                            />
                            <label
                                for="assignment-allow-resubmission"
                                class="text-sm text-gray-700 dark:text-gray-300"
                            >
                                Allow resubmission
                            </label>
                        </div>

                        <fieldset>
                            <legend
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Accepted File Types
                                <span class="ml-1 font-normal text-gray-500 dark:text-slate-400"
                                    >(leave all unchecked to accept any type)</span
                                >
                            </legend>
                            <div
                                class="flex flex-wrap gap-2"
                                aria-describedby="allowed-file-types-error"
                            >
                                <label
                                    v-for="option in FILE_TYPE_OPTIONS"
                                    :key="option.label"
                                    class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1 text-sm transition-colors"
                                    :class="
                                        isSelected(option)
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-400 dark:hover:border-slate-500'
                                    "
                                >
                                    <input
                                        :id="`file-type-${option.label}`"
                                        type="checkbox"
                                        :checked="isSelected(option)"
                                        class="sr-only"
                                        @change="toggleOption(option)"
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
                        class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 dark:border-slate-700 dark:bg-slate-900"
                    >
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :aria-busy="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                        >
                            <span v-if="form.processing">Saving&hellip;</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
