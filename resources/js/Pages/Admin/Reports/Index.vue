<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Head } from '@inertiajs/vue3';
import {
    DocumentTextIcon,
    ExclamationTriangleIcon,
    AcademicCapIcon,
    StarIcon,
} from '@heroicons/vue/24/outline';

interface Props {
    stats: {
        total_submissions: number;
        late_submissions: number;
        graded: number;
        released_grades: number;
    };
}

const props = defineProps<Props>();

const gradedPct = computed(() =>
    props.stats.total_submissions > 0
        ? Math.round((props.stats.graded / props.stats.total_submissions) * 100)
        : 0
);
const latePct = computed(() =>
    props.stats.total_submissions > 0
        ? Math.round((props.stats.late_submissions / props.stats.total_submissions) * 100)
        : 0
);
const releasedPct = computed(() =>
    props.stats.graded > 0
        ? Math.round((props.stats.released_grades / props.stats.graded) * 100)
        : 0
);
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Reports</h1>
                <a
                    :href="route('admin.reports.export')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    download
                >
                    Export CSV
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="stats-heading">
                        <h2 id="stats-heading" class="mb-6 text-lg font-semibold text-gray-900">
                            Summary Statistics
                        </h2>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <StatCard
                                label="Total Submissions"
                                :icon="DocumentTextIcon"
                                accent="blue"
                                :animation-delay="0"
                            >
                                {{ stats.total_submissions.toLocaleString() }}
                            </StatCard>
                            <StatCard
                                label="Late Submissions"
                                :icon="ExclamationTriangleIcon"
                                accent="red"
                                :animation-delay="80"
                            >
                                {{ stats.late_submissions.toLocaleString() }}
                            </StatCard>
                            <StatCard
                                label="Graded"
                                :icon="AcademicCapIcon"
                                accent="green"
                                :animation-delay="160"
                            >
                                {{ stats.graded.toLocaleString() }}
                            </StatCard>
                            <StatCard
                                label="Released Grades"
                                :icon="StarIcon"
                                accent="cyan"
                                :animation-delay="240"
                            >
                                {{ stats.released_grades.toLocaleString() }}
                            </StatCard>
                        </div>
                    </section>

                    <!-- Submission Breakdown -->
                    <section
                        class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                        aria-labelledby="breakdown-heading"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2 id="breakdown-heading" class="font-semibold text-gray-800">
                                Submission Breakdown
                            </h2>
                        </div>
                        <div class="space-y-4 px-6 py-5">
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="text-gray-600">Graded rate</span>
                                    <span class="font-medium text-gray-900">{{ gradedPct }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100">
                                    <div
                                        class="h-2 rounded-full bg-green-500 transition-all duration-700"
                                        :style="`width: ${gradedPct}%`"
                                    />
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="text-gray-600">Late rate</span>
                                    <span class="font-medium text-gray-900">{{ latePct }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100">
                                    <div
                                        class="h-2 rounded-full bg-red-400 transition-all duration-700"
                                        :style="`width: ${latePct}%`"
                                    />
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="text-gray-600">Grades released</span>
                                    <span class="font-medium text-gray-900"
                                        >{{ releasedPct }}%</span
                                    >
                                </div>
                                <div class="h-2 rounded-full bg-gray-100">
                                    <div
                                        class="h-2 rounded-full bg-cyan-500 transition-all duration-700"
                                        :style="`width: ${releasedPct}%`"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
