<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import type { Assignment, PaginatedResponse, Submission } from '@/Types/models';

const props = defineProps<{
    assignment: Assignment;
    submissions: PaginatedResponse<Submission>;
    canManage: boolean;
}>();
</script>

<template>
    <Head :title="`Submissions — ${assignment.title}`" />

    <main class="mx-auto max-w-5xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
                <li>
                    <Link :href="route('assignments.show', assignment.id)" class="hover:underline">
                        {{ assignment.title }}
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Submissions</li>
            </ol>
        </nav>

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                Submissions
                <span class="ml-2 text-lg font-normal text-gray-500"
                    >({{ submissions.meta.total }})</span
                >
            </h1>

            <a
                v-if="canManage"
                :href="route('assignments.submissions.export', assignment.id)"
                class="rounded border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                Export CSV
            </a>
        </div>

        <div
            v-if="submissions.data.length === 0"
            class="rounded border border-dashed border-gray-300 px-6 py-12 text-center text-gray-500"
            role="status"
        >
            No submissions yet.
        </div>

        <table v-else class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-left text-gray-600">
                    <th scope="col" class="py-3 pr-4 font-medium">Student</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Submitted At</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Attempt</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Files</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Status</th>
                    <th scope="col" class="py-3 font-medium">Grade</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="submission in submissions.data"
                    :key="submission.id"
                    class="border-b border-gray-100 hover:bg-gray-50"
                >
                    <td class="py-3 pr-4">
                        <Link
                            :href="route('submissions.show', submission.id)"
                            class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        >
                            {{ submission.student?.name }}
                        </Link>
                    </td>
                    <td class="py-3 pr-4 text-gray-600">
                        {{ new Date(submission.submitted_at).toLocaleString() }}
                        <span
                            v-if="submission.is_late"
                            class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700"
                        >
                            Late
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-gray-600">#{{ submission.attempt_no }}</td>
                    <td class="py-3 pr-4 text-gray-600">{{ submission.files?.length ?? 0 }}</td>
                    <td class="py-3 pr-4 text-gray-600 capitalize">{{ submission.status }}</td>
                    <td class="py-3 text-gray-600">
                        {{ submission.grade ? submission.grade.score : '—' }}
                        <span
                            v-if="submission.grade && !submission.grade.released_at"
                            class="ml-1 text-xs text-yellow-600"
                        >
                            (unreleased)
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination v-if="submissions.links.length > 3" :links="submissions.links" />
    </main>
</template>
