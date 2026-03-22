<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
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
                            class="hover:text-gray-700 focus:rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Create Assignment</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <main>
                    <section
                        aria-labelledby="create-assignment-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1
                                id="create-assignment-heading"
                                class="text-lg font-bold text-gray-900"
                            >
                                Create Assignment
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">
                                {{ section.course?.title }} &mdash;
                                {{ section.section_name }}
                            </p>
                        </header>

                        <form novalidate @submit.prevent="submit">
                            <div class="space-y-5 px-6 py-6">
                                <div>
                                    <InputLabel for="assignment-title">
                                        Title
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="assignment-title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
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
                                    <InputLabel for="assignment-instructions">
                                        Instructions
                                    </InputLabel>
                                    <textarea
                                        id="assignment-instructions"
                                        v-model="form.instructions"
                                        rows="5"
                                        aria-describedby="assignment-instructions-error"
                                        :aria-invalid="!!form.errors.instructions"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Describe what students need to do…"
                                    ></textarea>
                                    <InputError
                                        id="assignment-instructions-error"
                                        class="mt-1"
                                        :message="form.errors.instructions"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="assignment-due-at">Due Date</InputLabel>
                                    <InputText
                                        id="assignment-due-at"
                                        v-model="form.due_at"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
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
                                    <InputText
                                        id="assignment-max-score"
                                        v-model="form.max_score"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-1 block w-32"
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
                                    <legend
                                        id="allowed-file-types-legend"
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Accepted File Types
                                        <span class="ml-1 font-normal text-gray-500"
                                            >(leave all unchecked to accept any type)</span
                                        >
                                    </legend>
                                    <div
                                        class="grid grid-cols-2 gap-2"
                                        aria-describedby="allowed-file-types-error"
                                    >
                                        <div
                                            v-for="option in FILE_TYPE_OPTIONS"
                                            :key="option.mime"
                                            class="flex items-center gap-2"
                                        >
                                            <input
                                                :id="`file-type-${option.mime}`"
                                                v-model="form.allowed_file_types"
                                                type="checkbox"
                                                :value="option.mime"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <label
                                                :for="`file-type-${option.mime}`"
                                                class="text-sm text-gray-700"
                                            >
                                                {{ option.label }}
                                            </label>
                                        </div>
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
                                class="mx-6 mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700"
                            >
                                Please fix the errors above before submitting.
                            </div>

                            <footer
                                class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                            >
                                <Link :href="route('instructor.courses.index')">
                                    <Button type="button" severity="secondary">Cancel</Button>
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    :aria-busy="form.processing"
                                >
                                    <span v-if="form.processing">Creating&hellip;</span>
                                    <span v-else>Create Assignment</span>
                                </Button>
                            </footer>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
