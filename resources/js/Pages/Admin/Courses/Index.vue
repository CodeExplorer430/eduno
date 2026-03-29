<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

interface Course {
    id: number;
    code: string;
    title: string;
    department: string;
    status: 'draft' | 'published' | 'archived';
    sections: { id: number }[];
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta?: { current_page: number; last_page: number; total: number };
}

interface Summary {
    total: number;
    draft: number;
    published: number;
    archived: number;
}

interface Props {
    courses: Paginated<Course>;
    filters: { status?: string | null };
    statuses: { name: string; value: string }[];
    summary: Summary;
}

const props = defineProps<Props>();

const statusFilter = ref<string>(props.filters.status ?? '');

const statusBadge: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600',
    published: 'bg-green-100 text-green-700',
    archived: 'bg-amber-100 text-amber-700',
};

function applyFilter(): void {
    router.get(
        route('admin.courses.index'),
        { status: statusFilter.value || undefined },
        { preserveState: true, replace: true }
    );
}

watch(statusFilter, applyFilter);

const successMessage = computed(
    () => (usePage().props.flash as Record<string, string> | undefined)?.success ?? null
);

const showFlash = ref<boolean>(true);
watch(successMessage, () => {
    showFlash.value = true;
});

function changeStatus(course: Course, newStatus: string): void {
    if (newStatus === course.status) return;
    router.patch(
        route('admin.courses.updateStatus', course.id),
        { status: newStatus },
        { preserveScroll: true }
    );
}
</script>

<template>
    <Head title="Courses — Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Course Management</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Stats strip -->
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Total
                    </p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ summary.total }}
                    </p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Draft
                    </p>
                    <p class="mt-1 text-2xl font-bold text-gray-500 dark:text-slate-400">
                        {{ summary.draft }}
                    </p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600">
                        Published
                    </p>
                    <p class="mt-1 text-2xl font-bold text-green-700">{{ summary.published }}</p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">
                        Archived
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-700">{{ summary.archived }}</p>
                </div>
            </div>

            <!-- Flash message -->
            <div
                v-if="successMessage && showFlash"
                role="status"
                aria-live="polite"
                class="mb-4 flex items-center justify-between rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200"
            >
                <span>{{ successMessage }}</span>
                <button
                    type="button"
                    class="ml-4 rounded text-green-500 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    aria-label="Dismiss"
                    @click="showFlash = false"
                >
                    &times;
                </button>
            </div>

            <!-- Filter bar -->
            <section
                aria-label="Filter courses"
                class="mb-6 flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div>
                    <label
                        for="status_filter"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Status
                    </label>
                    <select
                        id="status_filter"
                        v-model="statusFilter"
                        class="block w-44 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">All Statuses</option>
                        <option v-for="s in statuses" :key="s.value" :value="s.value">
                            {{ s.name }}
                        </option>
                    </select>
                </div>
            </section>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">All Courses</h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100 dark:divide-slate-700"
                        aria-label="Course list"
                    >
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Code
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Title
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Department
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Sections
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
                                    <span class="sr-only">Change Status</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 bg-white dark:divide-slate-700 dark:bg-slate-800"
                        >
                            <tr v-if="courses.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-sm text-gray-400 dark:text-slate-500"
                                >
                                    No courses found.
                                </td>
                            </tr>
                            <tr
                                v-for="course in courses.data"
                                :key="course.id"
                                class="transition-colors hover:bg-gray-50 dark:hover:bg-slate-700/50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 font-mono text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ course.code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ course.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                                    {{ course.department }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                                    {{ course.sections.length }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            statusBadge[course.status] ??
                                            'bg-gray-100 text-gray-600'
                                        "
                                    >
                                        {{ course.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div
                                        class="inline-flex overflow-hidden rounded-lg border border-gray-200 text-xs dark:border-slate-600"
                                        role="group"
                                        :aria-label="`Change status for ${course.title}`"
                                    >
                                        <button
                                            v-for="s in statuses"
                                            :key="s.value"
                                            type="button"
                                            :class="
                                                course.status === s.value
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-white text-gray-600 hover:bg-gray-50 dark:bg-slate-700 dark:text-gray-400 dark:hover:bg-slate-600'
                                            "
                                            class="border-r border-gray-200 px-2 py-1 last:border-r-0 transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 dark:border-slate-600"
                                            :aria-pressed="course.status === s.value"
                                            @click="changeStatus(course, s.value)"
                                        >
                                            {{ s.name }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination
                v-if="courses.links && courses.links.length > 3"
                :links="courses.links"
                class="mt-4"
            />
        </div>
    </AuthenticatedLayout>
</template>
