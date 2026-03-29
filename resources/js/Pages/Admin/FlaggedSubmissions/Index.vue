<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useFormatDate } from '@/composables/useFormatDate';

interface FlaggedSubmission {
    id: number;
    submitted_at: string;
    is_late: boolean;
    student: { id: number; name: string };
    assignment: {
        id: number;
        title: string;
        course_section?: {
            section_name: string;
            course?: { title: string };
        };
    };
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    submissions: Paginated<FlaggedSubmission>;
}

defineProps<Props>();

const { formatDate } = useFormatDate();
</script>

<template>
    <Head title="Flagged Submissions — Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Flagged Submissions</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                v-if="submissions.data.length === 0"
                role="status"
                class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-slate-600 dark:bg-slate-800"
            >
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    No submissions are currently flagged for review.
                </p>
            </div>

            <div
                v-else
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-amber-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        {{ submissions.data.length }} flagged
                        {{ submissions.data.length === 1 ? 'submission' : 'submissions' }}
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100 dark:divide-slate-700"
                        aria-label="Flagged submissions"
                    >
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Student
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Assignment
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Course — Section
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Submitted At
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Late
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 bg-white dark:divide-slate-700 dark:bg-slate-800"
                        >
                            <tr
                                v-for="sub in submissions.data"
                                :key="sub.id"
                                class="transition-colors hover:bg-amber-50 dark:hover:bg-slate-700/50"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ sub.student.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ sub.assignment.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                                    {{ sub.assignment.course_section?.course?.title }}
                                    <span
                                        v-if="sub.assignment.course_section"
                                        class="text-gray-400 dark:text-slate-500"
                                    >
                                        — {{ sub.assignment.course_section.section_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                                    <time :datetime="sub.submitted_at">
                                        {{ formatDate(sub.submitted_at) }}
                                    </time>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        v-if="sub.is_late"
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                    >
                                        Late
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-slate-500"
                                        >On time</span
                                    >
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <Link
                                        :href="route('instructor.submissions.show', sub.id)"
                                        class="font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                                        :aria-label="`View submission by ${sub.student.name}`"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination
                v-if="submissions.links && submissions.links.length > 3"
                :links="submissions.links"
                class="mt-4"
            />
        </div>
    </AuthenticatedLayout>
</template>
