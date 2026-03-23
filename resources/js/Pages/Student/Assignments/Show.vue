<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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
    grade?: Grade | null;
    files?: { id: number }[];
}

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
    instructions: string | null;
    due_at: string | null;
    max_score: number;
    allow_resubmission: boolean;
    allowed_file_types: string[] | null;
    course_section_id: number;
    course_section?: {
        section_name: string;
        course?: { title: string };
    };
}

const props = defineProps<{
    assignment: Assignment;
    submission: Submission | null;
    submissions: Submission[];
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const statusBadge: Record<string, string> = {
    submitted: 'bg-blue-100 text-blue-700',
    graded: 'bg-green-100 text-green-700',
    returned: 'bg-purple-100 text-purple-700',
    late: 'bg-red-100 text-red-700',
    pending: 'bg-yellow-100 text-yellow-700',
};

const submissionStatus = computed<string>(() => {
    if (!props.submission) return 'pending';
    const s = props.submission.status;
    if (s === 'submitted' || s === 'graded' || s === 'returned') return s;
    return 'pending';
});

const gradeReleased = computed<boolean>(() => !!props.submission?.grade?.released_at);

const canSubmit = computed<boolean>(() => {
    if (!props.submission) return true;
    return props.assignment.allow_resubmission;
});

const dueUrgency = computed<'past' | 'soon' | 'normal'>(() => {
    if (!props.assignment.due_at) return 'normal';
    const due = new Date(props.assignment.due_at);
    const now = new Date();
    if (due < now) return 'past';
    if (due.getTime() - now.getTime() < 86400000) return 'soon';
    return 'normal';
});

const dueDateClass = computed<string>(() => {
    if (dueUrgency.value === 'past') return 'text-red-600 font-medium';
    if (dueUrgency.value === 'soon') return 'text-amber-600 font-medium';
    return 'text-gray-600';
});

const isPastDue = computed<boolean>(() => dueUrgency.value === 'past');
</script>

<template>
    <Head :title="assignment.title" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('student.assignments.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Assignments
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ assignment.title }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left: assignment details (wider) -->
                <div class="space-y-6 lg:col-span-2">
                    <section
                        aria-labelledby="assignment-heading"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <div
                            class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-4"
                        >
                            <div>
                                <h1 id="assignment-heading" class="text-lg font-bold text-gray-900">
                                    {{ assignment.title }}
                                </h1>
                                <p
                                    v-if="assignment.course_section"
                                    class="mt-0.5 text-sm text-gray-500"
                                >
                                    {{ assignment.course_section.course?.title }} —
                                    {{ assignment.course_section.section_name }}
                                </p>
                            </div>
                            <span
                                class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                :class="
                                    statusBadge[submissionStatus] ?? 'bg-gray-100 text-gray-600'
                                "
                            >
                                {{ submissionStatus }}
                            </span>
                        </div>

                        <div class="px-6 py-5">
                            <dl class="mb-5 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-700">Due Date</dt>
                                    <dd :class="dueDateClass">
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
                                <div>
                                    <dt class="font-medium text-gray-700">Resubmission</dt>
                                    <dd class="text-gray-600">
                                        {{
                                            assignment.allow_resubmission
                                                ? 'Allowed'
                                                : 'Not allowed'
                                        }}
                                    </dd>
                                </div>
                                <div
                                    v-if="
                                        assignment.allowed_file_types &&
                                        assignment.allowed_file_types.length > 0
                                    "
                                    class="col-span-2"
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

                            <div
                                v-if="assignment.instructions"
                                class="border-t border-gray-100 pt-4"
                            >
                                <h2 class="mb-2 text-sm font-semibold text-gray-700">
                                    Instructions
                                </h2>
                                <div
                                    class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-600"
                                >
                                    {{ assignment.instructions }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Attempt History -->
                    <section
                        aria-labelledby="attempt-history-heading"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 id="attempt-history-heading" class="font-semibold text-gray-900">
                                Attempt History
                            </h2>
                        </div>

                        <div
                            v-if="submissions.length === 0"
                            class="px-6 py-5 text-sm text-gray-500"
                        >
                            No submissions yet.
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-100 text-sm"
                                aria-label="Submission attempts"
                            >
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                        >
                                            Attempt
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                        >
                                            Submitted
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                        >
                                            Status
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                        >
                                            Files
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="attempt in submissions" :key="attempt.id">
                                        <td class="px-5 py-3 font-medium text-gray-900">
                                            #{{ attempt.attempt_no }}
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">
                                            {{ formatDate(attempt.submitted_at) }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                                :class="
                                                    statusBadge[attempt.status] ??
                                                    'bg-gray-100 text-gray-600'
                                                "
                                            >
                                                {{ attempt.status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">
                                            {{ attempt.files?.length ?? 0 }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-if="
                                assignment.allow_resubmission &&
                                !isPastDue &&
                                submissions.length > 0
                            "
                            class="border-t border-gray-100 px-6 py-4"
                        >
                            <Link
                                :href="route('student.submissions.create', assignment.id)"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Submit Again
                            </Link>
                        </div>
                    </section>

                    <!-- Submission status (if submitted) -->
                    <section
                        v-if="submission"
                        aria-labelledby="submission-heading"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 id="submission-heading" class="font-semibold text-gray-900">
                                Your Submission
                            </h2>
                        </div>

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
                                <div v-if="submission.is_late">
                                    <dt class="font-medium text-red-700">Late Submission</dt>
                                    <dd class="text-red-600">Yes</dd>
                                </div>
                            </dl>

                            <Link
                                :href="route('student.submissions.show', submission.id)"
                                class="mt-4 inline-flex items-center rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                View submission details &rarr;
                            </Link>
                        </div>

                        <div
                            v-if="gradeReleased && submission.grade"
                            class="border-t border-gray-100 bg-green-50 px-6 py-5"
                            role="status"
                            aria-label="Grade result"
                        >
                            <h3 class="mb-2 text-sm font-semibold text-green-800">Grade</h3>
                            <p class="text-2xl font-bold text-green-700">
                                {{ submission.grade.score }}
                                <span class="text-base font-normal text-green-600"
                                    >/ {{ assignment.max_score }}</span
                                >
                            </p>
                            <p v-if="submission.grade.feedback" class="mt-2 text-sm text-green-700">
                                <span class="font-medium">Feedback:</span>
                                {{ submission.grade.feedback }}
                            </p>
                        </div>
                    </section>
                </div>

                <!-- Right: submit panel -->
                <div>
                    <div
                        class="sticky top-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 class="font-semibold text-gray-900">Submission</h2>
                        </div>
                        <div class="px-5 py-5">
                            <Link
                                v-if="canSubmit"
                                :href="route('student.submissions.create', assignment.id)"
                                class="block w-full rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                {{ submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                            </Link>
                            <p v-else class="text-sm text-gray-500">
                                You have already submitted this assignment and resubmission is not
                                allowed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
