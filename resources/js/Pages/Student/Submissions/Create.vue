<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUpload from 'primevue/fileupload';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const MIME_LABELS: Record<string, string> = {
    'application/pdf': 'PDF',
    'application/msword': 'Word (.doc)',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word (.docx)',
    'application/vnd.ms-powerpoint': 'PowerPoint (.ppt)',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation':
        'PowerPoint (.pptx)',
    'application/zip': 'ZIP',
    'text/plain': 'Plain Text (.txt)',
    'image/png': 'PNG',
    'image/jpeg': 'JPG/JPEG',
    'application/vnd.ms-excel': 'Excel (.xls)',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'Excel (.xlsx)',
};

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    allowed_file_types: string[] | null;
    course_section_id: number;
}

const props = defineProps<{
    assignment: Assignment;
}>();

const form = useForm<{
    files: File[];
}>({
    files: [],
});

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const onFileSelect = (e: FileUploadSelectEvent): void => {
    form.files = e.files;
};

const submit = (): void => {
    form.post(route('student.submissions.store', props.assignment.id), {
        forceFormData: true,
    });
};

const hasFiles = computed<boolean>(() => form.files.length > 0);
</script>

<template>
    <Head :title="`Submit — ${assignment.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('student.assignments.index')"
                            class="hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                        >
                            Assignments
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li>
                        <Link
                            :href="route('student.assignments.show', assignment.id)"
                            class="hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                        >
                            {{ assignment.title }}
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Submit</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <main>
                    <section
                        aria-labelledby="submit-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1 id="submit-heading" class="text-lg font-bold text-gray-900">
                                Submit Assignment
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">{{ assignment.title }}</p>
                        </header>

                        <!-- Assignment summary -->
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                            <dl class="flex flex-wrap gap-8 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-700">Due Date</dt>
                                    <dd class="text-gray-600">
                                        {{
                                            assignment.due_at
                                                ? formatDate(assignment.due_at)
                                                : 'No due date'
                                        }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Max Score</dt>
                                    <dd class="text-gray-600">{{ assignment.max_score }} pts</dd>
                                </div>
                                <div
                                    v-if="
                                        assignment.allowed_file_types &&
                                        assignment.allowed_file_types.length > 0
                                    "
                                >
                                    <dt class="font-medium text-gray-700">Accepted File Types</dt>
                                    <dd class="text-gray-600">
                                        {{
                                            assignment.allowed_file_types
                                                .map((m) => MIME_LABELS[m] ?? m)
                                                .join(', ')
                                        }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <form novalidate @submit.prevent="submit">
                            <div class="px-6 py-6">
                                <div role="group" aria-labelledby="files-label">
                                    <p
                                        id="files-label"
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Submission Files
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </p>

                                    <FileUpload
                                        mode="advanced"
                                        :multiple="true"
                                        :auto="false"
                                        aria-describedby="files-error"
                                        @select="onFileSelect"
                                    />

                                    <InputError
                                        id="files-error"
                                        class="mt-2"
                                        :message="form.errors.files"
                                    />
                                </div>
                            </div>

                            <!-- Form status message -->
                            <div
                                v-if="form.wasSuccessful"
                                role="status"
                                aria-live="polite"
                                class="mx-6 mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700"
                            >
                                Your submission was uploaded successfully.
                            </div>

                            <div
                                v-if="form.hasErrors && !form.errors.files"
                                role="alert"
                                aria-live="assertive"
                                class="mx-6 mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700"
                            >
                                There was a problem with your submission. Please check the fields
                                above.
                            </div>

                            <footer
                                class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                            >
                                <Link
                                    :href="route('student.assignments.show', assignment.id)"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>

                                <button
                                    type="submit"
                                    :disabled="form.processing || !hasFiles"
                                    :aria-busy="form.processing"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Uploading&hellip;</span>
                                    <span v-else>Submit Assignment</span>
                                </button>
                            </footer>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
