<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GradeForm from '@/Components/GradeForm.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Grade {
    id: number;
    score: number;
    feedback: string | null;
    released_at: string | null;
}

interface Props {
    submission: {
        id: number;
        status: string;
        submitted_at: string;
        is_late: boolean;
        attempt_no: number;
        assignment: { id: number; title: string; max_score: number };
        student: { id: number; name: string };
        files: Array<{ id: number; original_name: string; size_bytes: number }>;
        grade?: Grade | null;
    };
}

const props = defineProps<Props>();

const validStatuses = ['submitted', 'graded', 'returned', 'late', 'pending'] as const;
type ValidStatus = (typeof validStatuses)[number];
const safeStatus = (s: string): ValidStatus =>
    validStatuses.includes(s as ValidStatus) ? (s as ValidStatus) : 'pending';

const formatBytes = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const releaseForm = useForm({});
const releaseGrade = (): void => {
    if (!props.submission.grade) return;
    releaseForm.patch(route('instructor.grades.release', props.submission.grade.id));
};
</script>

<template>
    <Head :title="`Submission — ${submission.student.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.submissions.index', submission.assignment.id)"
                            class="hover:text-gray-700 focus:rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            Submissions
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ submission.student.name }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <main>
                    <!-- Submission details -->
                    <section
                        aria-labelledby="submission-details-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1
                                id="submission-details-heading"
                                class="text-lg font-bold text-gray-900"
                            >
                                {{ submission.assignment.title }}
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">
                                Submitted by
                                <strong>{{ submission.student.name }}</strong>
                            </p>
                        </header>

                        <dl class="divide-y divide-gray-100 px-6">
                            <div class="flex items-center gap-4 py-3">
                                <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">
                                    Status
                                </dt>
                                <dd>
                                    <StatusBadge :variant="safeStatus(submission.status)" />
                                    <span
                                        v-if="submission.is_late"
                                        class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                    >
                                        Late
                                    </span>
                                </dd>
                            </div>
                            <div class="flex items-center gap-4 py-3">
                                <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">
                                    Submitted
                                </dt>
                                <dd class="text-sm text-gray-700">
                                    <time :datetime="submission.submitted_at">
                                        {{ formatDate(submission.submitted_at) }}
                                    </time>
                                </dd>
                            </div>
                            <div class="flex items-center gap-4 py-3">
                                <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">
                                    Attempt
                                </dt>
                                <dd class="text-sm text-gray-700">#{{ submission.attempt_no }}</dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Files -->
                    <section
                        aria-labelledby="files-heading"
                        class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h2 id="files-heading" class="text-base font-semibold text-gray-900">
                                Submitted Files
                            </h2>
                        </header>
                        <ul
                            v-if="submission.files.length > 0"
                            class="divide-y divide-gray-100"
                            aria-label="Submitted files"
                        >
                            <li
                                v-for="file in submission.files"
                                :key="file.id"
                                class="flex items-center justify-between px-6 py-3"
                            >
                                <span class="truncate text-sm text-gray-800">
                                    {{ file.original_name }}
                                </span>
                                <span class="ml-4 shrink-0 text-xs text-gray-400">
                                    {{ formatBytes(file.size_bytes) }}
                                </span>
                            </li>
                        </ul>
                        <p v-else class="px-6 py-4 text-sm text-gray-500">No files attached.</p>
                    </section>

                    <!-- Grade form -->
                    <section
                        aria-labelledby="grading-heading"
                        class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h2 id="grading-heading" class="text-base font-semibold text-gray-900">
                                Grading
                            </h2>
                        </header>
                        <div class="px-6 py-6">
                            <GradeForm
                                :submission-id="submission.id"
                                :max-score="submission.assignment.max_score"
                                :existing-grade="submission.grade"
                            />

                            <div
                                v-if="submission.grade && !submission.grade.released_at"
                                class="mt-6 border-t border-gray-100 pt-4"
                            >
                                <p class="mb-3 text-sm text-gray-600">
                                    Grade is saved but not yet visible to the student.
                                </p>
                                <form @submit.prevent="releaseGrade">
                                    <PrimaryButton
                                        type="submit"
                                        :disabled="releaseForm.processing"
                                        :aria-busy="releaseForm.processing"
                                    >
                                        <span v-if="releaseForm.processing">Releasing&hellip;</span>
                                        <span v-else>Release Grade to Student</span>
                                    </PrimaryButton>
                                </form>
                            </div>

                            <div
                                v-else-if="submission.grade?.released_at"
                                role="status"
                                aria-live="polite"
                                class="mt-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700"
                            >
                                Grade released on
                                {{ formatDate(submission.grade.released_at) }}.
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
