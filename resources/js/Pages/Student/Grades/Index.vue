<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { ChartBarIcon } from '@heroicons/vue/24/outline';

interface GradeEntry {
    id: number;
    score: number;
    feedback: string | null;
    released_at: string;
    submission?: {
        assignment?: {
            title: string;
            max_score: number;
            course_section?: {
                section_name: string;
                course?: { title: string };
            };
        };
    };
}

const props = defineProps<{
    grades: GradeEntry[];
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));

const averageScore = computed<number | null>(() => {
    const valid = props.grades.filter((g) => g.submission?.assignment?.max_score);
    if (valid.length === 0) return null;
    const sum = valid.reduce((acc, g) => {
        const max = g.submission!.assignment!.max_score;
        return acc + (g.score / max) * 100;
    }, 0);
    return Math.round(sum / valid.length);
});

function scorePercent(score: number, maxScore: number | undefined): number {
    if (!maxScore) return 0;
    return Math.min(100, Math.round((score / maxScore) * 100));
}

function scoreBarClass(pct: number): string {
    if (pct >= 90) return 'bg-green-500';
    if (pct >= 75) return 'bg-blue-500';
    if (pct >= 60) return 'bg-amber-500';
    return 'bg-red-500';
}

function gradeBadgeClass(pct: number): string {
    if (pct >= 90) return 'bg-green-100 text-green-700';
    if (pct >= 75) return 'bg-blue-100 text-blue-700';
    if (pct >= 60) return 'bg-amber-100 text-amber-700';
    return 'bg-red-100 text-red-700';
}

function gradeLetter(pct: number): string {
    if (pct >= 93) return 'A';
    if (pct >= 90) return 'A−';
    if (pct >= 87) return 'B+';
    if (pct >= 83) return 'B';
    if (pct >= 80) return 'B−';
    if (pct >= 77) return 'C+';
    if (pct >= 73) return 'C';
    if (pct >= 70) return 'C−';
    if (pct >= 60) return 'D';
    return 'F';
}
</script>

<template>
    <Head title="My Grades" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">My Grades</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Average stat card -->
            <div v-if="averageScore !== null" class="mb-6">
                <div
                    class="overflow-hidden rounded-xl bg-white px-6 py-5 shadow-sm ring-1 ring-gray-100 sm:max-w-xs"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Average Score
                    </p>
                    <p
                        class="mt-1 text-3xl font-bold"
                        :class="averageScore >= 75 ? 'text-green-600' : 'text-red-600'"
                    >
                        {{ averageScore }}%
                    </p>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Across {{ grades.length }} grade{{ grades.length !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            <section aria-labelledby="grades-table-heading">
                <h2 id="grades-table-heading" class="sr-only">Grades table</h2>

                <EmptyState
                    v-if="grades.length === 0"
                    :icon="ChartBarIcon"
                    title="No grades available yet."
                    description="Grades will appear here once your instructor releases them."
                />

                <!-- Mobile card list -->
                <ul v-else class="block sm:hidden space-y-3" aria-label="My grades">
                    <li
                        v-for="grade in grades"
                        :key="grade.id"
                        class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100"
                    >
                        <p class="text-sm font-semibold text-gray-900">
                            {{ grade.submission?.assignment?.course_section?.course?.title ?? '—' }}
                            <span class="font-normal text-gray-400 text-xs ml-1">
                                {{
                                    grade.submission?.assignment?.course_section?.section_name ?? ''
                                }}
                            </span>
                        </p>
                        <p class="mt-0.5 text-sm text-gray-600">
                            {{ grade.submission?.assignment?.title ?? '—' }}
                        </p>
                        <div class="mt-2 flex items-center gap-3">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="
                                    gradeBadgeClass(
                                        scorePercent(
                                            grade.score,
                                            grade.submission?.assignment?.max_score
                                        )
                                    )
                                "
                            >
                                {{
                                    gradeLetter(
                                        scorePercent(
                                            grade.score,
                                            grade.submission?.assignment?.max_score
                                        )
                                    )
                                }}
                            </span>
                            <span class="text-sm font-medium text-gray-800">
                                {{ grade.score }}
                                <span class="font-normal text-gray-400"
                                    >/ {{ grade.submission?.assignment?.max_score ?? '?' }}</span
                                >
                            </span>
                        </div>
                        <p v-if="grade.feedback" class="mt-2 line-clamp-2 text-xs text-gray-500">
                            {{ grade.feedback }}
                        </p>
                        <p class="mt-2 text-xs text-gray-400">
                            {{ formatDate(grade.released_at) }}
                        </p>
                    </li>
                </ul>

                <div
                    v-if="grades.length > 0"
                    class="hidden sm:block overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100" aria-label="My grades">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Course
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Assignment
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Score
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Grade
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Feedback
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Released
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="grade in grades"
                                    :key="grade.id"
                                    class="transition-colors hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <template
                                            v-if="grade.submission?.assignment?.course_section"
                                        >
                                            <span class="font-medium">
                                                {{
                                                    grade.submission.assignment.course_section
                                                        .course?.title ?? '—'
                                                }}
                                            </span>
                                            <span class="mt-0.5 block text-xs text-gray-500">
                                                {{
                                                    grade.submission.assignment.course_section
                                                        .section_name
                                                }}
                                            </span>
                                        </template>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{ grade.submission?.assignment?.title ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-2 w-24 overflow-hidden rounded-full bg-gray-100"
                                            >
                                                <div
                                                    class="h-full rounded-full transition-all"
                                                    :class="
                                                        scoreBarClass(
                                                            scorePercent(
                                                                grade.score,
                                                                grade.submission?.assignment
                                                                    ?.max_score
                                                            )
                                                        )
                                                    "
                                                    :style="{
                                                        width: `${scorePercent(grade.score, grade.submission?.assignment?.max_score)}%`,
                                                    }"
                                                    :aria-label="`${scorePercent(grade.score, grade.submission?.assignment?.max_score)}%`"
                                                ></div>
                                            </div>
                                            <span
                                                class="font-semibold text-gray-900"
                                                :aria-label="`Score: ${grade.score} out of ${grade.submission?.assignment?.max_score ?? '?'}`"
                                            >
                                                {{ grade.score }}
                                                <span class="font-normal text-gray-400"
                                                    >/
                                                    {{
                                                        grade.submission?.assignment?.max_score ??
                                                        '?'
                                                    }}</span
                                                >
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold">
                                        {{
                                            gradeLetter(
                                                scorePercent(
                                                    grade.score,
                                                    grade.submission?.assignment?.max_score
                                                )
                                            )
                                        }}
                                    </td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-600">
                                        <span
                                            v-if="grade.feedback"
                                            class="line-clamp-2"
                                            :title="grade.feedback"
                                        >
                                            {{ grade.feedback }}
                                        </span>
                                        <span v-else class="text-gray-400">No feedback</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ formatDate(grade.released_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
