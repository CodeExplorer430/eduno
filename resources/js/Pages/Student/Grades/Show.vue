<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface GradeDetail {
    id: number;
    score: number;
    feedback: string | null;
    released_at: string;
    assignment: {
        id: number;
        title: string;
        max_score: number;
        course_section_id: number;
    };
}

defineProps<{
    grade: GradeDetail;
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));
</script>

<template>
    <Head :title="`Grade — ${grade.assignment.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('student.grades.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            My Grades
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ grade.assignment.title }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h1 id="grade-heading" class="font-semibold text-gray-900">
                        {{ grade.assignment.title }}
                    </h1>
                </div>

                <div class="px-6 py-6">
                    <dl class="divide-y divide-gray-100">
                        <div class="flex items-start gap-4 py-3">
                            <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">Score</dt>
                            <dd
                                class="text-sm font-semibold text-gray-900"
                                :aria-label="`Score: ${grade.score} out of ${grade.assignment.max_score}`"
                            >
                                {{ grade.score }}
                                <span class="font-normal text-gray-400">
                                    / {{ grade.assignment.max_score }}
                                </span>
                            </dd>
                        </div>

                        <div class="flex items-start gap-4 py-3">
                            <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">
                                Released
                            </dt>
                            <dd class="text-sm text-gray-700">
                                <time :datetime="grade.released_at">
                                    {{ formatDate(grade.released_at) }}
                                </time>
                            </dd>
                        </div>

                        <div class="flex items-start gap-4 py-3">
                            <dt class="w-32 shrink-0 text-sm font-medium text-gray-500">
                                Feedback
                            </dt>
                            <dd class="text-sm text-gray-700">
                                <span v-if="grade.feedback">{{ grade.feedback }}</span>
                                <span v-else class="text-gray-400">No feedback provided.</span>
                            </dd>
                        </div>
                    </dl>

                    <div
                        role="status"
                        aria-live="polite"
                        class="mt-6 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700"
                    >
                        This grade has been released by your instructor.
                    </div>

                    <div class="mt-6">
                        <Link
                            :href="route('student.assignments.show', grade.assignment.id)"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Back to Assignment
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
