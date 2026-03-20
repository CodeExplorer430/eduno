<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    report: {
        total_courses: number;
        total_sections: number;
        total_students: number;
        total_submissions: number;
        late_submissions: number;
        graded_submissions: number;
    };
}>();

const latePercent = computed(() => {
    if (props.report.total_submissions === 0) return 0;
    return Math.round((props.report.late_submissions / props.report.total_submissions) * 100);
});

const gradedPercent = computed(() => {
    if (props.report.total_submissions === 0) return 0;
    return Math.round((props.report.graded_submissions / props.report.total_submissions) * 100);
});
</script>

<template>
    <Head title="Reports" />

    <main class="mx-auto max-w-5xl px-4 py-8">
        <h1 class="mb-6 text-2xl font-bold">System Reports</h1>

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Courses</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.total_courses }}
                </dd>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Sections</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.total_sections }}
                </dd>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.total_students }}
                </dd>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Submissions</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.total_submissions }}
                </dd>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Late Submissions</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.late_submissions }}
                    <span class="ml-2 text-lg font-normal text-gray-500">({{ latePercent }}%)</span>
                </dd>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Graded Submissions</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ report.graded_submissions }}
                    <span class="ml-2 text-lg font-normal text-gray-500"
                        >({{ gradedPercent }}%)</span
                    >
                </dd>
            </div>
        </dl>
    </main>
</template>
