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
            <h1 class="text-xl font-bold text-gray-900">Flagged Submissions</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                v-if="submissions.data.length === 0"
                role="status"
                class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
            >
                <p class="text-sm text-gray-500">
                    No submissions are currently flagged for review.
                </p>
            </div>

            <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-amber-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900">
                        {{ submissions.data.length }} flagged
                        {{ submissions.data.length === 1 ? 'submission' : 'submissions' }}
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100"
                        aria-label="Flagged submissions"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Student
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
                                    Course — Section
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Submitted At
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Late
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr
                                v-for="sub in submissions.data"
                                :key="sub.id"
                                class="transition-colors hover:bg-amber-50"
                            >
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ sub.student.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ sub.assignment.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ sub.assignment.course_section?.course?.title }}
                                    <span
                                        v-if="sub.assignment.course_section"
                                        class="text-gray-400"
                                    >
                                        — {{ sub.assignment.course_section.section_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
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
                                    <span v-else class="text-gray-400">On time</span>
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
