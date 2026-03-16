<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

interface SubmissionFile {
    id: number;
    original_name: string;
    size_bytes: number;
}

interface Grade {
    score: number;
    feedback: string | null;
    released_at: string | null;
}

interface Submission {
    id: number;
    status: string;
    submitted_at: string;
    is_late: boolean;
    attempt_no: number;
    assignment: {
        id: number;
        title: string;
        max_score: number;
        course_section?: {
            section_name: string;
            course?: { title: string };
        };
    };
    files: SubmissionFile[];
    grade?: Grade | null;
}

const props = defineProps<{
    submission: Submission;
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const formatSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

const submissionStatus = computed<'submitted' | 'graded' | 'returned' | 'late' | 'pending'>(() => {
    const s = props.submission.status;
    if (s === 'submitted' || s === 'graded' || s === 'returned') return s;
    return 'pending';
});

const gradeReleased = computed<boolean>(() => !!props.submission.grade?.released_at);
</script>

<template>
    <Head :title="`Submission — ${submission.assignment.title}`" />

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
                            :href="route('student.assignments.show', submission.assignment.id)"
                            class="hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                        >
                            {{ submission.assignment.title }}
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Submission</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <!-- Submission details -->
                <section
                    aria-labelledby="submission-heading"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <header class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h1 id="submission-heading" class="text-xl font-bold text-gray-900">
                                    {{ submission.assignment.title }}
                                </h1>
                                <p
                                    v-if="submission.assignment.course_section"
                                    class="mt-0.5 text-sm text-gray-500"
                                >
                                    {{ submission.assignment.course_section.course?.title }} &mdash;
                                    {{ submission.assignment.course_section.section_name }}
                                </p>
                            </div>
                            <StatusBadge :status="submissionStatus" />
                        </div>
                    </header>

                    <div class="px-6 py-5">
                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="font-medium text-gray-700">Submitted At</dt>
                                <dd class="text-gray-600">
                                    {{ formatDate(submission.submitted_at) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-700">Attempt</dt>
                                <dd class="text-gray-600">#{{ submission.attempt_no }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-700">Max Score</dt>
                                <dd class="text-gray-600">
                                    {{ submission.assignment.max_score }} pts
                                </dd>
                            </div>
                            <div v-if="submission.is_late">
                                <dt class="font-medium text-red-700">Late Submission</dt>
                                <dd class="text-red-600 font-medium">Yes</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <!-- Submitted files -->
                <section
                    aria-labelledby="files-heading"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <header class="border-b border-gray-100 px-6 py-4">
                        <h2 id="files-heading" class="font-semibold text-gray-800">
                            Submitted Files
                        </h2>
                    </header>

                    <div class="px-6 py-5">
                        <ul
                            v-if="submission.files.length > 0"
                            class="divide-y divide-gray-100"
                            aria-label="Submitted files"
                        >
                            <li
                                v-for="file in submission.files"
                                :key="file.id"
                                class="flex items-center justify-between py-3 text-sm"
                            >
                                <span class="flex items-center gap-2 text-gray-800">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 shrink-0 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                        />
                                    </svg>
                                    {{ file.original_name }}
                                </span>
                                <span class="ms-4 shrink-0 text-xs text-gray-400">
                                    {{ formatSize(file.size_bytes) }}
                                </span>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-gray-400">
                            No files attached to this submission.
                        </p>
                    </div>
                </section>

                <!-- Grade (if released) -->
                <section
                    v-if="gradeReleased && submission.grade"
                    aria-labelledby="grade-heading"
                    class="overflow-hidden rounded-lg bg-green-50 shadow-sm"
                >
                    <header class="border-b border-green-100 px-6 py-4">
                        <h2 id="grade-heading" class="font-semibold text-green-800">Grade</h2>
                    </header>

                    <div class="px-6 py-5" role="status" aria-label="Grade result">
                        <p class="text-3xl font-bold text-green-700">
                            {{ submission.grade.score }}
                            <span class="text-lg font-normal text-green-600">
                                / {{ submission.assignment.max_score }}
                            </span>
                        </p>

                        <div v-if="submission.grade.feedback" class="mt-4">
                            <h3 class="mb-1 text-sm font-medium text-green-800">
                                Instructor Feedback
                            </h3>
                            <p class="text-sm text-green-700 whitespace-pre-wrap">
                                {{ submission.grade.feedback }}
                            </p>
                        </div>

                        <p class="mt-3 text-xs text-green-600">
                            Graded on {{ formatDate(submission.grade.released_at!) }}
                        </p>
                    </div>
                </section>

                <!-- Pending grade notice -->
                <div
                    v-else-if="!gradeReleased"
                    role="status"
                    aria-live="polite"
                    class="rounded-lg border border-yellow-200 bg-yellow-50 px-6 py-4 text-sm text-yellow-700"
                >
                    Your submission has been received and is awaiting grading.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
