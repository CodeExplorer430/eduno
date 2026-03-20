<script setup lang="ts">
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useFileSize } from '@/composables/useFileSize';
import type { Assignment, Submission } from '@/Types/models';

const props = defineProps<{
    assignment: Assignment;
    canManage: boolean;
    submissions?: Submission[];
    mySubmission?: Submission | null;
}>();

const { formatBytes } = useFileSize();

const publishForm = useForm({});
const selectedFiles = ref<File[]>([]);
const isSubmitting = ref(false);
const uploadProgress = ref<number | null>(null);
const fileErrors = ref<string | null>(null);

function togglePublish(): void {
    publishForm.post(route('assignments.publish', props.assignment.id));
}

function handleFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    selectedFiles.value = input.files ? Array.from(input.files) : [];
    fileErrors.value = null;
    const oversized = selectedFiles.value.find((f) => f.size > 26_214_400);
    if (oversized) {
        fileErrors.value = `"${oversized.name}" exceeds the 25 MB limit. Please choose a smaller file.`;
    }
}

function submitAssignment(): void {
    if (selectedFiles.value.length === 0) return;
    isSubmitting.value = true;
    uploadProgress.value = 0;
    const fd = new FormData();
    selectedFiles.value.forEach((f) => fd.append('files[]', f));
    router.post(route('assignments.submissions.store', props.assignment.id), fd, {
        onProgress: (progress) => {
            uploadProgress.value = progress?.percentage ?? null;
        },
        onError: (errors) => {
            fileErrors.value = errors['files'] ?? errors['files.0'] ?? null;
            isSubmitting.value = false;
            uploadProgress.value = null;
        },
        onSuccess: () => {
            isSubmitting.value = false;
            uploadProgress.value = null;
        },
    });
}

const selectedFileSummary = computed(() => {
    if (selectedFiles.value.length === 0) return null;
    const total = selectedFiles.value.reduce((sum, f) => sum + f.size, 0);
    return `${selectedFiles.value.length} file(s) selected · ${formatBytes(total)}`;
});

function isPastDue(): boolean {
    return !!props.assignment.due_at && new Date(props.assignment.due_at) < new Date();
}
</script>

<template>
    <Head :title="assignment.title" />

    <main class="mx-auto max-w-4xl px-4 py-8">
        <Breadcrumb
            class="mb-4"
            :crumbs="[
                {
                    label: 'Assignments',
                    href: route('sections.assignments.index', assignment.course_section_id),
                },
                { label: assignment.title },
            ]"
        />

        <article>
            <header class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ assignment.title }}</h1>
                    <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                        <span>Max score: {{ assignment.max_score }}</span>
                        <span v-if="assignment.due_at">
                            Due: {{ new Date(assignment.due_at).toLocaleString() }}
                            <span v-if="isPastDue()" class="ml-1 text-red-600 font-medium"
                                >(Past Due)</span
                            >
                        </span>
                        <span v-if="assignment.allow_resubmission" class="text-blue-600"
                            >Resubmission allowed</span
                        >
                    </div>
                </div>

                <div v-if="canManage" class="flex shrink-0 gap-2">
                    <button
                        type="button"
                        class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        @click="togglePublish"
                    >
                        {{ assignment.published_at ? 'Unpublish' : 'Publish' }}
                    </button>
                    <Link
                        :href="route('assignments.edit', assignment.id)"
                        class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                    >
                        Edit
                    </Link>
                </div>
            </header>

            <section v-if="assignment.instructions" class="mb-8">
                <h2 class="mb-2 text-lg font-semibold">Instructions</h2>
                <div class="prose max-w-none text-gray-800">
                    <p class="whitespace-pre-wrap">{{ assignment.instructions }}</p>
                </div>
            </section>

            <!-- Instructor: submissions list -->
            <section v-if="canManage && submissions !== undefined">
                <h2 class="mb-4 text-lg font-semibold">
                    Submissions
                    <span class="ml-2 text-sm font-normal text-gray-500"
                        >({{ submissions.length }})</span
                    >
                </h2>

                <div v-if="submissions.length === 0" class="text-sm text-gray-500" role="status">
                    No submissions yet.
                </div>

                <table v-else class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-600">
                            <th scope="col" class="py-3 pr-4 font-medium">Student</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Submitted</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Attempt</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Status</th>
                            <th scope="col" class="py-3 font-medium">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="submission in submissions"
                            :key="submission.id"
                            class="border-b border-gray-100 hover:bg-gray-50"
                        >
                            <td class="py-3 pr-4">
                                <Link
                                    :href="route('submissions.show', submission.id)"
                                    class="text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                                >
                                    {{ submission.student?.name }}
                                </Link>
                            </td>
                            <td class="py-3 pr-4 text-gray-600">
                                {{ new Date(submission.submitted_at).toLocaleString() }}
                                <StatusBadge
                                    v-if="submission.is_late"
                                    variant="late"
                                    class="ml-1"
                                />
                            </td>
                            <td class="py-3 pr-4 text-gray-600">#{{ submission.attempt_no }}</td>
                            <td class="py-3 pr-4 text-gray-600 capitalize">
                                {{ submission.status }}
                            </td>
                            <td class="py-3 text-gray-600">
                                {{ submission.grade ? submission.grade.score : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Student: own submission status / upload form -->
            <section v-else-if="!canManage">
                <div v-if="mySubmission" class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                    <h2 class="mb-2 text-lg font-semibold">Your Submission</h2>
                    <p class="text-sm text-gray-600">
                        Submitted: {{ new Date(mySubmission.submitted_at).toLocaleString() }}
                        <StatusBadge v-if="mySubmission.is_late" variant="late" class="ml-1" />
                    </p>
                    <p class="mt-1 text-sm text-gray-600 capitalize">
                        Status: {{ mySubmission.status }}
                    </p>
                    <p class="mt-1 text-sm text-gray-600">Attempt #{{ mySubmission.attempt_no }}</p>
                    <Link
                        :href="route('submissions.show', mySubmission.id)"
                        class="mt-3 inline-block text-sm text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                    >
                        View submission details
                    </Link>
                </div>

                <div v-else class="mt-4">
                    <h2 class="mb-4 text-lg font-semibold">Submit Your Work</h2>

                    <p
                        v-if="!assignment.allow_resubmission"
                        role="note"
                        class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800"
                    >
                        You have 1 attempt. Make sure your file is correct before submitting.
                    </p>

                    <form novalidate @submit.prevent="submitAssignment">
                        <div>
                            <label for="files" class="block text-sm font-medium text-gray-700">
                                Files <span aria-hidden="true">*</span>
                            </label>
                            <input
                                id="files"
                                type="file"
                                multiple
                                accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg"
                                required
                                class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                                :aria-describedby="fileErrors ? 'files-error' : undefined"
                                :aria-invalid="!!fileErrors"
                                @change="handleFileChange"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Accepted: PDF, DOC, DOCX, ZIP, PNG, JPG, JPEG. Max 25 MB per file.
                            </p>
                            <p
                                v-if="selectedFileSummary"
                                class="mt-1 text-xs text-gray-500"
                                aria-live="polite"
                            >
                                {{ selectedFileSummary }}
                            </p>
                            <p
                                v-if="fileErrors"
                                id="files-error"
                                class="mt-1 text-sm text-red-600"
                                role="alert"
                            >
                                {{ fileErrors }}
                            </p>
                        </div>

                        <div v-if="uploadProgress !== null" class="mt-3">
                            <div
                                role="progressbar"
                                :aria-valuenow="uploadProgress"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                class="h-2 w-full overflow-hidden rounded bg-gray-200"
                            >
                                <div
                                    class="h-full bg-indigo-500 transition-all"
                                    :style="{ width: `${uploadProgress}%` }"
                                />
                            </div>
                            <p class="mt-1 text-xs text-gray-500" aria-live="polite">
                                Uploading… {{ uploadProgress }}%
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="isSubmitting || selectedFiles.length === 0 || !!fileErrors"
                            class="mt-4 rounded bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-60"
                        >
                            {{ isSubmitting ? 'Submitting…' : 'Submit Assignment' }}
                        </button>
                    </form>
                </div>
            </section>
        </article>
    </main>
</template>
