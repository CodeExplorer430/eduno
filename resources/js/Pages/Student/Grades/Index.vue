<script setup lang="ts">
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

defineProps<{
    grades: GradeEntry[];
}>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));
</script>

<template>
    <Head title="My Grades" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">My Grades</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="grades-table-heading">
                        <h2 id="grades-table-heading" class="sr-only">Grades table</h2>

                        <div
                            v-if="grades.length > 0"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                    aria-label="My grades"
                                >
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
                                            class="transition hover:bg-gray-50"
                                        >
                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                <template
                                                    v-if="
                                                        grade.submission?.assignment?.course_section
                                                    "
                                                >
                                                    <span class="font-medium">
                                                        {{
                                                            grade.submission.assignment
                                                                .course_section.course?.title ?? '—'
                                                        }}
                                                    </span>
                                                    <span
                                                        class="mt-0.5 block text-xs text-gray-500"
                                                    >
                                                        {{
                                                            grade.submission.assignment
                                                                .course_section.section_name
                                                        }}
                                                    </span>
                                                </template>
                                                <span v-else class="text-gray-400">—</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                {{ grade.submission?.assignment?.title ?? '—' }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-sm font-semibold text-gray-900"
                                            >
                                                <span
                                                    :aria-label="`Score: ${grade.score} out of ${grade.submission?.assignment?.max_score ?? '?'}`"
                                                >
                                                    {{ grade.score }}
                                                    <span class="font-normal text-gray-400">
                                                        /
                                                        {{
                                                            grade.submission?.assignment
                                                                ?.max_score ?? '?'
                                                        }}
                                                    </span>
                                                </span>
                                            </td>
                                            <td class="max-w-xs px-6 py-4 text-sm text-gray-600">
                                                <span
                                                    v-if="grade.feedback"
                                                    class="line-clamp-2"
                                                    :title="grade.feedback"
                                                >
                                                    {{ grade.feedback }}
                                                </span>
                                                <span v-else class="text-gray-400"
                                                    >No feedback</span
                                                >
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ formatDate(grade.released_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <EmptyState
                            v-else
                            :icon="ChartBarIcon"
                            title="No grades available yet."
                            description="Grades will appear here once your instructor releases them."
                        />
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
