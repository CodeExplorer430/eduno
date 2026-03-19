<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Submission } from '@/Types/models';

const props = defineProps<{
    submission: Submission;
    isInstructor: boolean;
}>();

const gradeForm = useForm({
    score: props.submission.grade?.score ?? ('' as number | string),
    feedback: props.submission.grade?.feedback ?? '',
});

const releaseForm = useForm({});

function submitGrade(): void {
    if (props.submission.grade) {
        gradeForm.patch(route('grades.update', props.submission.grade.id));
    } else {
        gradeForm.post(route('submissions.grade.store', props.submission.id));
    }
}

function releaseGrade(): void {
    if (props.submission.grade) {
        releaseForm.post(route('grades.release', props.submission.grade.id));
    }
}
</script>

<template>
    <Head :title="`Submission — ${submission.student?.name}`" />

    <main class="mx-auto max-w-3xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
                <li>
                    <Link
                        :href="route('assignments.show', submission.assignment_id)"
                        class="hover:underline"
                    >
                        {{ submission.assignment?.title }}
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Submission</li>
            </ol>
        </nav>

        <h1 class="mb-2 text-2xl font-bold">Submission by {{ submission.student?.name }}</h1>
        <p class="mb-6 text-sm text-gray-500">
            Submitted {{ new Date(submission.submitted_at).toLocaleString() }}
            <span
                v-if="submission.is_late"
                class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700"
            >
                Late
            </span>
            &middot; Attempt #{{ submission.attempt_no }}
        </p>

        <!-- Files -->
        <section class="mb-8">
            <h2 class="mb-3 text-lg font-semibold">Files</h2>
            <ul class="space-y-2" role="list">
                <li
                    v-for="file in submission.files"
                    :key="file.id"
                    class="flex items-center justify-between rounded border border-gray-200 px-4 py-3 text-sm"
                >
                    <span class="font-medium text-gray-800">{{ file.original_name }}</span>
                    <span class="text-gray-500">{{ (file.size_bytes / 1024).toFixed(1) }} KB</span>
                </li>
            </ul>
            <p v-if="!submission.files?.length" class="text-sm text-gray-500" role="status">
                No files attached.
            </p>
        </section>

        <!-- Instructor: grade form -->
        <section v-if="isInstructor" class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <h2 class="mb-4 text-lg font-semibold">Grade</h2>

            <form novalidate @submit.prevent="submitGrade">
                <div class="space-y-4">
                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700">
                            Score <span aria-hidden="true">*</span>
                        </label>
                        <input
                            id="score"
                            v-model="gradeForm.score"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="mt-1 block w-40 rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :aria-describedby="gradeForm.errors.score ? 'score-error' : undefined"
                            :aria-invalid="!!gradeForm.errors.score"
                        />
                        <p
                            v-if="gradeForm.errors.score"
                            id="score-error"
                            class="mt-1 text-sm text-red-600"
                            role="alert"
                        >
                            {{ gradeForm.errors.score }}
                        </p>
                    </div>

                    <div>
                        <label for="feedback" class="block text-sm font-medium text-gray-700"
                            >Feedback</label
                        >
                        <textarea
                            id="feedback"
                            v-model="gradeForm.feedback"
                            rows="4"
                            class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :aria-describedby="
                                gradeForm.errors.feedback ? 'feedback-error' : undefined
                            "
                            :aria-invalid="!!gradeForm.errors.feedback"
                        />
                        <p
                            v-if="gradeForm.errors.feedback"
                            id="feedback-error"
                            class="mt-1 text-sm text-red-600"
                            role="alert"
                        >
                            {{ gradeForm.errors.feedback }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button
                        type="submit"
                        :disabled="gradeForm.processing"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-60"
                    >
                        {{ submission.grade ? 'Update Grade' : 'Save Grade' }}
                    </button>

                    <button
                        v-if="submission.grade && !submission.grade.released_at"
                        type="button"
                        :disabled="releaseForm.processing"
                        class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-60"
                        @click="releaseGrade"
                    >
                        Release to Student
                    </button>

                    <span
                        v-if="submission.grade?.released_at"
                        class="flex items-center text-sm text-green-700"
                        role="status"
                    >
                        Released {{ new Date(submission.grade.released_at).toLocaleString() }}
                    </span>
                </div>
            </form>
        </section>

        <!-- Student: released grade view -->
        <section v-else>
            <div
                v-if="submission.grade?.released_at"
                class="rounded-lg border border-green-200 bg-green-50 p-5"
            >
                <h2 class="mb-3 text-lg font-semibold text-green-900">Your Grade</h2>
                <p class="text-2xl font-bold text-green-800">
                    {{ submission.grade.score }}
                    <span class="text-base font-normal text-green-700">
                        / {{ submission.assignment?.max_score }}
                    </span>
                </p>
                <div v-if="submission.grade.feedback" class="mt-3">
                    <p class="text-sm font-medium text-gray-700">Feedback:</p>
                    <p class="mt-1 whitespace-pre-wrap text-sm text-gray-800">
                        {{ submission.grade.feedback }}
                    </p>
                </div>
            </div>

            <div
                v-else
                class="rounded-lg border border-gray-200 bg-gray-50 p-5 text-sm text-gray-500"
                role="status"
            >
                Your grade has not been released yet.
            </div>
        </section>
    </main>
</template>
