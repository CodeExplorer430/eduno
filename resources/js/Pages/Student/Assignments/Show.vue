<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Tag from 'primevue/tag';
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
}

interface Assignment {
    id: number;
    title: string;
    instructions: string | null;
    due_at: string | null;
    max_score: number;
    allow_resubmission: boolean;
    course_section_id: number;
    course_section?: {
        section_name: string;
        course?: { title: string };
    };
}

const props = defineProps<{
    assignment: Assignment;
    submission: Submission | null;
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const statusSeverity: Record<string, 'info' | 'success' | 'secondary' | 'danger' | 'warn'> = {
    submitted: 'info',
    graded: 'success',
    returned: 'secondary',
    late: 'danger',
    pending: 'warn',
};

const submissionStatus = computed<'submitted' | 'graded' | 'returned' | 'late' | 'pending'>(() => {
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
                            class="hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
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

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <!-- Assignment Details -->
                <section
                    aria-labelledby="assignment-heading"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <header class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h1 id="assignment-heading" class="text-xl font-bold text-gray-900">
                                    {{ assignment.title }}
                                </h1>
                                <p
                                    v-if="assignment.course_section"
                                    class="mt-0.5 text-sm text-gray-500"
                                >
                                    {{ assignment.course_section.course?.title }} &mdash;
                                    {{ assignment.course_section.section_name }}
                                </p>
                            </div>
                            <Tag
                                :severity="statusSeverity[submissionStatus] ?? 'warn'"
                                :value="submissionStatus"
                                class="capitalize"
                            />
                        </div>
                    </header>

                    <div class="px-6 py-5">
                        <dl class="mb-5 grid grid-cols-2 gap-4 text-sm">
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
                            <div>
                                <dt class="font-medium text-gray-700">Resubmission</dt>
                                <dd class="text-gray-600">
                                    {{ assignment.allow_resubmission ? 'Allowed' : 'Not allowed' }}
                                </dd>
                            </div>
                        </dl>

                        <div v-if="assignment.instructions" class="border-t border-gray-100 pt-4">
                            <h2 class="mb-2 text-sm font-semibold text-gray-700">Instructions</h2>
                            <div
                                class="prose prose-sm max-w-none text-gray-600 whitespace-pre-wrap"
                            >
                                {{ assignment.instructions }}
                            </div>
                        </div>
                    </div>

                    <footer class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                        <Link
                            v-if="canSubmit"
                            :href="route('student.submissions.create', assignment.id)"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                        </Link>
                        <p v-else class="text-sm text-gray-500">
                            You have already submitted this assignment and resubmission is not
                            allowed.
                        </p>
                    </footer>
                </section>

                <!-- Submission status -->
                <section
                    v-if="submission"
                    aria-labelledby="submission-heading"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <header class="border-b border-gray-100 px-6 py-4">
                        <h2 id="submission-heading" class="font-semibold text-gray-800">
                            Your Submission
                        </h2>
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
                            <div v-if="submission.is_late">
                                <dt class="font-medium text-red-700">Late Submission</dt>
                                <dd class="text-red-600">Yes</dd>
                            </div>
                        </dl>

                        <Link
                            :href="route('student.submissions.show', submission.id)"
                            class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                        >
                            View submission details &rarr;
                        </Link>
                    </div>

                    <!-- Grade (if released) -->
                    <div
                        v-if="gradeReleased && submission.grade"
                        class="border-t border-gray-100 bg-green-50 px-6 py-5"
                        role="status"
                        aria-label="Grade result"
                    >
                        <h3 class="mb-3 text-sm font-semibold text-green-800">Grade</h3>
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
        </div>
    </AuthenticatedLayout>
</template>
