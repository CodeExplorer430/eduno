<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Grade {
    id: number;
    score: number | null;
    released_at: string | null;
}

interface SubmissionItem {
    id: number;
    status: string;
    submitted_at: string;
    is_late: boolean;
    student: { id: number; name: string };
    assignment: {
        id: number;
        title: string;
        max_score: number;
        course_section?: {
            section_name: string;
            course?: { title: string };
        };
    };
    grade?: Grade | null;
}

interface Paginator {
    data: SubmissionItem[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

defineProps<{ submissions: Paginator }>();

const statusBadge: Record<string, string> = {
    submitted: 'bg-blue-100 text-blue-700',
    graded: 'bg-green-100 text-green-700',
    returned: 'bg-purple-100 text-purple-700',
    late: 'bg-red-100 text-red-700',
    pending: 'bg-yellow-100 text-yellow-700',
};

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));
</script>

<template>
    <Head title="All Submissions" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                All Submissions
            </h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100 dark:divide-slate-700"
                        aria-label="All submissions"
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
                                    Course
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
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Score
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">View</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 bg-white dark:divide-slate-700 dark:bg-slate-800"
                        >
                            <tr v-if="submissions.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-8 text-center text-sm text-gray-400 dark:text-slate-500"
                                >
                                    No submissions yet.
                                </td>
                            </tr>
                            <tr
                                v-for="sub in submissions.data"
                                :key="sub.id"
                                class="transition-colors hover:bg-gray-50 dark:hover:bg-slate-700/50"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ sub.student.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ sub.assignment.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <span>{{
                                        sub.assignment.course_section?.course?.title ?? '—'
                                    }}</span>
                                    <span class="block text-xs text-gray-400 dark:text-slate-500">
                                        {{ sub.assignment.course_section?.section_name ?? '' }}
                                    </span>
                                </td>
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-slate-400"
                                >
                                    <time :datetime="sub.submitted_at">{{
                                        formatDate(sub.submitted_at)
                                    }}</time>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            statusBadge[sub.status] ??
                                            'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400'
                                        "
                                    >
                                        {{ sub.is_late ? 'late' : sub.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <template
                                        v-if="
                                            sub.grade?.score !== null &&
                                            sub.grade?.score !== undefined
                                        "
                                    >
                                        {{ sub.grade.score }} / {{ sub.assignment.max_score }}
                                    </template>
                                    <span v-else class="text-gray-400 dark:text-slate-500">—</span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <Link
                                        :href="route('instructor.submissions.show', sub.id)"
                                        class="font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="submissions.last_page > 1"
                    class="flex items-center justify-between border-t border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <Link
                        v-if="submissions.prev_page_url"
                        :href="submissions.prev_page_url"
                        class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-slate-700"
                    >
                        Previous
                    </Link>
                    <span v-else class="px-3 py-1.5 text-sm text-gray-400 dark:text-slate-500"
                        >Previous</span
                    >

                    <span class="text-sm text-gray-500 dark:text-slate-400">
                        Page {{ submissions.current_page }} of {{ submissions.last_page }}
                    </span>

                    <Link
                        v-if="submissions.next_page_url"
                        :href="submissions.next_page_url"
                        class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-slate-700"
                    >
                        Next
                    </Link>
                    <span v-else class="px-3 py-1.5 text-sm text-gray-400 dark:text-slate-500"
                        >Next</span
                    >
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
