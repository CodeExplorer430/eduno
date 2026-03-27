<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import SubmissionRow from '@/Components/SubmissionRow.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { FlagIcon } from '@heroicons/vue/24/outline';

interface Submission {
    id: number;
    student: { id: number; name: string };
    submitted_at: string;
    is_late: boolean;
    attempt_no: number;
    status: string;
    flagged_for_review: boolean;
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

const showFlaggedOnly = ref(false);

const filteredSubmissions = computed<Submission[]>(() =>
    showFlaggedOnly.value
        ? props.submissions.filter((s) => s.flagged_for_review)
        : props.submissions
);
</script>

<template>
    <Head :title="`Submissions — ${assignment.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'Courses', href: route('instructor.courses.index') },
                    { label: 'Submissions' },
                ]"
            />
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <section aria-labelledby="gradebook-heading">
                <header class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h1 id="gradebook-heading" class="text-xl font-bold text-gray-900">
                            {{ assignment.title }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ assignment.course_section?.course?.title }} —
                            {{ assignment.course_section?.section_name }}
                            &middot; Max score: {{ assignment.max_score }}
                        </p>
                    </div>
                    <button
                        type="button"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-lg border px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-500',
                            showFlaggedOnly
                                ? 'border-amber-400 bg-amber-50 text-amber-700 hover:bg-amber-100'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                        ]"
                        :aria-pressed="showFlaggedOnly"
                        aria-label="Toggle show flagged submissions only"
                        @click="showFlaggedOnly = !showFlaggedOnly"
                    >
                        <FlagIcon class="h-4 w-4" aria-hidden="true" />
                        Flagged only
                    </button>
                    <a
                        :href="route('instructor.submissions.export', assignment.id)"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Export submissions as CSV"
                    >
                        Export CSV
                    </a>
                </header>

                <div
                    v-if="filteredSubmissions.length === 0"
                    role="status"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                >
                    <p class="text-sm text-gray-500">No submissions yet.</p>
                </div>

                <div
                    v-else
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-100"
                            aria-label="Student submissions"
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
                                        Submitted
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Late
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Attempt
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
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <SubmissionRow
                                    v-for="sub in filteredSubmissions"
                                    :key="sub.id"
                                    :submission="sub"
                                    :max-score="assignment.max_score"
                                />
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
