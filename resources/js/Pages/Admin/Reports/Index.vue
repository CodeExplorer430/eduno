<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_submissions: number;
        late_submissions: number;
        graded: number;
        released_grades: number;
    };
}

defineProps<Props>();

interface StatCard {
    label: string;
    key: keyof Props['stats'];
    description: string;
    colorClass: string;
}

const cards: StatCard[] = [
    {
        label: 'Total Submissions',
        key: 'total_submissions',
        description: 'All submissions across all assignments',
        colorClass: 'bg-blue-50 text-blue-700',
    },
    {
        label: 'Late Submissions',
        key: 'late_submissions',
        description: 'Submissions received after the due date',
        colorClass: 'bg-red-50 text-red-700',
    },
    {
        label: 'Graded',
        key: 'graded',
        description: 'Submissions that have received a grade',
        colorClass: 'bg-green-50 text-green-700',
    },
    {
        label: 'Released Grades',
        key: 'released_grades',
        description: 'Grades visible to students',
        colorClass: 'bg-purple-50 text-purple-700',
    },
];
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

                        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div
                                v-for="card in cards"
                                :key="card.key"
                                class="overflow-hidden rounded-lg bg-white shadow-sm px-6 py-5"
                            >
                                <dt class="truncate text-sm font-medium text-gray-500">
                                    {{ card.label }}
                                </dt>
                                <dd class="mt-2" :aria-label="`${card.label}: ${stats[card.key]}`">
                                    <span class="text-3xl font-bold" :class="card.colorClass">
                                        {{ stats[card.key].toLocaleString() }}
                                    </span>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ card.description }}
                                    </p>
                                </dd>
                            </div>
                        </dl>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
