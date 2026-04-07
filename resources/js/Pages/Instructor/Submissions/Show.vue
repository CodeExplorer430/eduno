<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GradeForm from '@/Components/GradeForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useAppToast } from '@/composables/useAppToast';
import { FlagIcon } from '@heroicons/vue/24/outline';

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
        flagged_for_review: boolean;
        assignment: { id: number; title: string; max_score: number };
        student: { id: number; name: string };
        files: Array<{ id: number; original_name: string; size_bytes: number }>;
        grade?: Grade | null;
    };
}

const props = defineProps<Props>();

const statusBadge: Record<string, string> = {
    submitted: 'bg-[#dbe1ff] text-[#00174b]',
    graded: 'bg-green-100 text-green-700',
    returned: 'bg-purple-100 text-purple-700',
    late: 'bg-red-50 text-[#ba1a1a]',
    pending: 'bg-yellow-100 text-yellow-700',
};

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

const appToast = useAppToast();
const releaseForm = useForm({});
const flagForm = useForm({});

watch(
    () => releaseForm.wasSuccessful,
    (val) => {
        if (val) appToast.success('Grade released.');
    }
);

watch(
    () => flagForm.wasSuccessful,
    (val) => {
        if (val)
            appToast.success(
                props.submission.flagged_for_review
                    ? 'Submission flagged for review.'
                    : 'Flag removed.'
            );
    }
);

const releaseGrade = (): void => {
    if (!props.submission.grade) return;
    releaseForm.patch(route('instructor.grades.release', props.submission.grade.id));
};

const toggleFlag = (): void => {
    flagForm.patch(route('instructor.submissions.flag', props.submission.id));
};
</script>

<template>
    <Head :title="`Submission — ${submission.student.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-[#434655] dark:text-slate-400">
                    <li>
                        <Link
                            :href="route('instructor.submissions.index', submission.assignment.id)"
                            class="rounded hover:text-[#141b2b] focus:outline-none focus:ring-2 focus:ring-[#004ac6] dark:hover:text-gray-200"
                        >
                            Submissions
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-[#141b2b] dark:text-gray-200" aria-current="page">
                        {{ submission.student.name }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Flagged warning banner -->
            <div
                v-if="submission.flagged_for_review"
                role="alert"
                class="mb-6 flex items-center gap-3 rounded-xl bg-amber-50 px-5 py-4 ring-1 ring-amber-200"
            >
                <FlagIcon class="h-5 w-5 shrink-0 text-amber-500" aria-hidden="true" />
                <p class="text-sm font-medium text-amber-800">
                    This submission has been flagged for review.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Left: submission details + files -->
                <div class="space-y-6">
                    <section
                        aria-labelledby="submission-details-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div
                            class="flex items-center justify-between gap-3 bg-[#f1f3ff] px-6 py-4 dark:bg-slate-900"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-4 w-1 rounded-full bg-[#004ac6]"
                                    aria-hidden="true"
                                ></div>
                                <div>
                                    <h1
                                        id="submission-details-heading"
                                        class="font-semibold text-[#141b2b] dark:text-white"
                                    >
                                        {{ submission.assignment.title }}
                                    </h1>
                                    <p class="text-xs text-[#434655] dark:text-slate-400">
                                        Submitted by <strong>{{ submission.student.name }}</strong>
                                    </p>
                                </div>
                            </div>
                            <!-- Flag toggle -->
                            <form @submit.prevent="toggleFlag">
                                <button
                                    type="submit"
                                    :disabled="flagForm.processing"
                                    :aria-busy="flagForm.processing"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1"
                                    :class="
                                        submission.flagged_for_review
                                            ? 'bg-amber-100 text-amber-700 hover:bg-amber-200'
                                            : 'bg-[#f1f3ff] text-[#434655] hover:bg-[#dbe1ff] dark:bg-slate-700 dark:text-gray-400 dark:hover:bg-slate-600'
                                    "
                                    :aria-label="
                                        submission.flagged_for_review
                                            ? 'Remove plagiarism flag'
                                            : 'Flag for plagiarism review'
                                    "
                                    :aria-pressed="submission.flagged_for_review"
                                >
                                    <FlagIcon class="h-3.5 w-3.5" aria-hidden="true" />
                                    {{ submission.flagged_for_review ? 'Flagged' : 'Flag' }}
                                </button>
                            </form>
                        </div>

                        <dl class="px-6 dark:divide-slate-700">
                            <div class="flex items-center gap-4 py-3">
                                <dt
                                    class="w-28 shrink-0 text-sm font-medium text-[#434655] dark:text-slate-400"
                                >
                                    Status
                                </dt>
                                <dd class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            statusBadge[submission.status] ??
                                            'bg-[#f1f3ff] text-[#434655] dark:bg-slate-700 dark:text-gray-400'
                                        "
                                    >
                                        {{ submission.status }}
                                    </span>
                                    <span
                                        v-if="submission.is_late"
                                        class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-[#ba1a1a]"
                                    >
                                        Late
                                    </span>
                                </dd>
                            </div>
                            <div class="flex items-center gap-4 py-3">
                                <dt
                                    class="w-28 shrink-0 text-sm font-medium text-[#434655] dark:text-slate-400"
                                >
                                    Submitted
                                </dt>
                                <dd class="text-sm text-[#141b2b] dark:text-gray-300">
                                    <time :datetime="submission.submitted_at">
                                        {{ formatDate(submission.submitted_at) }}
                                    </time>
                                </dd>
                            </div>
                            <div class="flex items-center gap-4 py-3">
                                <dt
                                    class="w-28 shrink-0 text-sm font-medium text-[#434655] dark:text-slate-400"
                                >
                                    Attempt
                                </dt>
                                <dd class="text-sm text-[#141b2b] dark:text-gray-300">
                                    #{{ submission.attempt_no }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section
                        aria-labelledby="files-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div
                            class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4 dark:bg-slate-900"
                        >
                            <div class="h-4 w-1 rounded-full bg-[#004ac6]" aria-hidden="true"></div>
                            <h2
                                id="files-heading"
                                class="font-semibold text-[#141b2b] dark:text-white"
                            >
                                Submitted Files
                            </h2>
                        </div>
                        <ul v-if="submission.files.length > 0" aria-label="Submitted files">
                            <li
                                v-for="file in submission.files"
                                :key="file.id"
                                class="flex items-center justify-between px-6 py-3 hover:bg-[#f1f3ff] transition-colors"
                            >
                                <span class="truncate text-sm text-[#141b2b] dark:text-gray-200">{{
                                    file.original_name
                                }}</span>
                                <span
                                    class="ml-4 shrink-0 text-xs text-[#737686] dark:text-slate-500"
                                    >{{ formatBytes(file.size_bytes) }}</span
                                >
                            </li>
                        </ul>
                        <p v-else class="px-6 py-4 text-sm text-[#434655] dark:text-slate-400">
                            No files attached.
                        </p>
                    </section>
                </div>

                <!-- Right: grading -->
                <section
                    aria-labelledby="grading-heading"
                    class="overflow-hidden rounded-2xl bg-[#f1f3ff]"
                >
                    <div class="flex items-center gap-3 rounded-t-2xl bg-white px-6 py-4">
                        <div class="h-4 w-1 rounded-full bg-[#004ac6]" aria-hidden="true"></div>
                        <h2
                            id="grading-heading"
                            class="font-semibold text-[#141b2b] dark:text-white"
                        >
                            Grading
                        </h2>
                    </div>
                    <div class="px-6 py-6">
                        <GradeForm
                            :submission-id="submission.id"
                            :max-score="submission.assignment.max_score"
                            :existing-grade="submission.grade"
                        />

                        <div
                            v-if="submission.grade && !submission.grade.released_at"
                            class="mt-6 pt-4"
                        >
                            <p class="mb-3 text-sm text-[#434655] dark:text-gray-400">
                                Grade is saved but not yet visible to the student.
                            </p>
                            <form @submit.prevent="releaseGrade">
                                <button
                                    type="submit"
                                    :disabled="releaseForm.processing"
                                    :aria-busy="releaseForm.processing"
                                    class="rounded-lg bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] px-4 py-2 text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#004ac6] disabled:opacity-60"
                                >
                                    <span v-if="releaseForm.processing">Releasing&hellip;</span>
                                    <span v-else>Release Grade to Student</span>
                                </button>
                            </form>
                        </div>

                        <div
                            v-else-if="submission.grade?.released_at"
                            role="status"
                            aria-live="polite"
                            class="mt-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700"
                        >
                            Grade released on {{ formatDate(submission.grade.released_at) }}.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
