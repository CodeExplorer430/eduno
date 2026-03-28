<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Chart from 'primevue/chart';
import { Head } from '@inertiajs/vue3';
import {
    DocumentTextIcon,
    ExclamationTriangleIcon,
    AcademicCapIcon,
    StarIcon,
    ArrowDownTrayIcon,
} from '@heroicons/vue/24/outline';

const exportFormat = ref<'csv' | 'pdf' | 'xlsx'>('csv');

function exportUrl(): string {
    return route('admin.reports.export') + '?format=' + exportFormat.value;
}

interface ChartDataset {
    submission_trend: { labels: string[]; onTime: number[]; late: number[] };
    grade_distribution: { labels: string[]; counts: number[] };
    late_rate_by_course: { labels: string[]; rates: number[] };
    status_breakdown: { labels: string[]; counts: number[] };
}

interface Props {
    stats: {
        total_submissions: number;
        late_submissions: number;
        graded: number;
        released_grades: number;
    };
    charts: ChartDataset;
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

// ── Chart 1: Submission Trend (line) ────────────────────────────
const trendData = computed(() => ({
    labels: props.charts.submission_trend.labels,
    datasets: [
        {
            label: 'On Time',
            data: props.charts.submission_trend.onTime,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.12)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
        },
        {
            label: 'Late',
            data: props.charts.submission_trend.late,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239,68,68,0.08)',
            fill: false,
            tension: 0.4,
            pointRadius: 4,
        },
    ],
}));

const trendOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'top' as const } },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1 },
            grid: { color: 'rgba(0,0,0,0.05)' },
        },
        x: { grid: { color: 'rgba(0,0,0,0.05)' } },
    },
};

// ── Chart 2: Grade Distribution (bar) ───────────────────────────
const gradeDistData = computed(() => ({
    labels: props.charts.grade_distribution.labels,
    datasets: [
        {
            label: 'Grades',
            data: props.charts.grade_distribution.counts,
            backgroundColor: ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e', '#10b981'],
            borderRadius: 4,
        },
    ],
}));

const gradeDistOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1 },
            grid: { color: 'rgba(0,0,0,0.05)' },
        },
        x: { grid: { display: false } },
    },
};

// ── Chart 3: Status Breakdown (doughnut) ────────────────────────
const statusColors: Record<string, string> = {
    submitted: '#3b82f6',
    graded: '#22c55e',
    returned: '#8b5cf6',
    late: '#ef4444',
    pending: '#f59e0b',
};

const statusData = computed(() => ({
    labels: props.charts.status_breakdown.labels,
    datasets: [
        {
            data: props.charts.status_breakdown.counts,
            backgroundColor: props.charts.status_breakdown.labels.map(
                (l) => statusColors[l] ?? '#94a3b8'
            ),
            hoverOffset: 6,
        },
    ],
}));

const statusOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: { legend: { position: 'right' as const } },
};

// ── Chart 4: Late Rate by Course (horizontal bar) ────────────────
const lateRateData = computed(() => ({
    labels: props.charts.late_rate_by_course.labels,
    datasets: [
        {
            label: 'Late Rate (%)',
            data: props.charts.late_rate_by_course.rates,
            backgroundColor: 'rgba(239,68,68,0.75)',
            borderRadius: 4,
        },
    ],
}));

const lateRateOptions = {
    indexAxis: 'y' as const,
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: {
            min: 0,
            max: 100,
            ticks: { callback: (v: number | string) => `${v}%` },
            grid: { color: 'rgba(0,0,0,0.05)' },
        },
        y: { grid: { display: false } },
    },
};
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Reports</h1>
                <div class="flex items-center gap-2">
                    <label for="export-format" class="sr-only">Export format</label>
                    <select
                        id="export-format"
                        v-model="exportFormat"
                        class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                        <option value="xlsx">Excel</option>
                    </select>
                    <a
                        :href="exportUrl()"
                        class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        download
                    >
                        <ArrowDownTrayIcon class="h-4 w-4" aria-hidden="true" />
                        Export
                    </a>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <main>
                    <!-- Summary Statistics -->
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

                    <!-- Submission Breakdown (progress bars) -->
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

                    <!-- Chart 1: Submission Trend -->
                    <section
                        class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                        aria-labelledby="trend-heading"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2 id="trend-heading" class="font-semibold text-gray-800">
                                Submission Trend — Last 8 Weeks
                            </h2>
                        </div>
                        <div class="px-6 py-5">
                            <figure>
                                <figcaption class="sr-only">
                                    Line chart showing weekly on-time and late submission counts
                                    over the past 8 weeks.
                                </figcaption>
                                <div class="h-64">
                                    <Chart
                                        type="line"
                                        :data="trendData"
                                        :options="trendOptions"
                                        class="h-full w-full"
                                    />
                                </div>
                            </figure>
                        </div>
                    </section>

                    <!-- Charts 2 & 3: Grade Distribution + Status Breakdown (side by side) -->
                    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Chart 2: Grade Distribution -->
                        <section
                            class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                            aria-labelledby="grade-dist-heading"
                        >
                            <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                                <div class="h-4 w-1 rounded-full bg-green-500" aria-hidden="true" />
                                <h2 id="grade-dist-heading" class="font-semibold text-gray-800">
                                    Grade Distribution
                                </h2>
                            </div>
                            <div class="px-6 py-5">
                                <figure>
                                    <figcaption class="sr-only">
                                        Bar chart showing the number of grades in each score band:
                                        0–49, 50–59, 60–69, 70–79, 80–89, and 90–100.
                                    </figcaption>
                                    <div class="h-56">
                                        <Chart
                                            type="bar"
                                            :data="gradeDistData"
                                            :options="gradeDistOptions"
                                            class="h-full w-full"
                                        />
                                    </div>
                                </figure>
                            </div>
                        </section>

                        <!-- Chart 3: Submission Status Breakdown -->
                        <section
                            class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                            aria-labelledby="status-heading"
                        >
                            <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                                <div
                                    class="h-4 w-1 rounded-full bg-purple-500"
                                    aria-hidden="true"
                                />
                                <h2 id="status-heading" class="font-semibold text-gray-800">
                                    Submission Status Breakdown
                                </h2>
                            </div>
                            <div class="px-6 py-5">
                                <figure>
                                    <figcaption class="sr-only">
                                        Doughnut chart showing the proportion of submissions in each
                                        status: submitted, graded, returned, and others.
                                    </figcaption>
                                    <div class="h-56">
                                        <Chart
                                            type="doughnut"
                                            :data="statusData"
                                            :options="statusOptions"
                                            class="h-full w-full"
                                        />
                                    </div>
                                </figure>
                            </div>
                        </section>
                    </div>

                    <!-- Chart 4: Late Rate by Course -->
                    <section
                        class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                        aria-labelledby="late-rate-heading"
                    >
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-red-500" aria-hidden="true" />
                            <h2 id="late-rate-heading" class="font-semibold text-gray-800">
                                Late Submission Rate by Course
                            </h2>
                        </div>
                        <div class="px-6 py-5">
                            <figure>
                                <figcaption class="sr-only">
                                    Horizontal bar chart showing the percentage of late submissions
                                    for each of the top 10 courses by submission volume.
                                </figcaption>
                                <div
                                    :style="`height: ${Math.max(160, charts.late_rate_by_course.labels.length * 36)}px`"
                                >
                                    <Chart
                                        type="bar"
                                        :data="lateRateData"
                                        :options="lateRateOptions"
                                        class="h-full w-full"
                                    />
                                </div>
                            </figure>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
