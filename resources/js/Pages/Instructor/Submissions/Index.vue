<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SubmissionRow from '@/Components/SubmissionRow.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Submission {
    id: number;
    student: { id: number; name: string };
    submitted_at: string;
    is_late: boolean;
    attempt_no: number;
    status: string;
    grade?: { score: number; released_at: string | null } | null;
}

interface Props {
    assignment: {
        id: number;
        title: string;
        max_score: number;
        course_section?: { section_name: string; course?: { title: string } };
    };
    submissions: Submission[];
}

const props = defineProps<Props>();
</script>

<template>
    <Head :title="`Submissions — ${assignment.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.courses.index')"
                            class="hover:text-gray-700 focus:rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Submissions</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="gradebook-heading">
                        <header class="mb-6">
                            <h1 id="gradebook-heading" class="text-xl font-bold text-gray-900">
                                {{ assignment.title }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ assignment.course_section?.course?.title }} &mdash;
                                {{ assignment.course_section?.section_name }}
                                &middot; Max score: {{ assignment.max_score }}
                            </p>
                        </header>

                        <div
                            v-if="submissions.length === 0"
                            role="status"
                            class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                        >
                            <p class="text-sm text-gray-500">No submissions yet.</p>
                        </div>

                        <div v-else class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                    aria-label="Student submissions"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Student
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Submitted
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Late
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Attempt
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Score
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Status
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <SubmissionRow
                                            v-for="sub in submissions"
                                            :key="sub.id"
                                            :submission="sub"
                                            :max-score="assignment.max_score"
                                        />
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
