<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import Button from 'primevue/button';

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

interface Props {
    courses: Paginated<Course>;
    filters: { status?: string | null };
    statuses: { name: string; value: string }[];
}

const props = defineProps<Props>();

const statusFilter = ref<string>(props.filters.status ?? '');

const statusSeverity: Record<string, 'secondary' | 'success' | 'warn' | 'danger'> = {
    draft: 'secondary',
    published: 'success',
    archived: 'warn',
};

function getSeverity(status: string): 'secondary' | 'success' | 'warn' | 'danger' {
    return statusSeverity[status] ?? 'secondary';
}

function applyFilter(): void {
    router.get(
        route('admin.courses.index'),
        { status: statusFilter.value || undefined },
        { preserveState: true, replace: true }
    );
}

const pendingStatus = ref<Record<number, string>>({});

const successMessage = computed(
    () => (usePage().props.flash as Record<string, string> | undefined)?.success ?? null
);

function changeStatus(course: Course): void {
    const newStatus = pendingStatus.value[course.id];
    if (!newStatus || newStatus === course.status) return;

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
            <h1 class="text-xl font-bold text-gray-900">Course Management</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <!-- Filter bar -->
                    <section aria-label="Filter courses" class="mb-6 flex items-end gap-4">
                        <div>
                            <label
                                for="status_filter"
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Status
                            </label>
                            <Select
                                id="status_filter"
                                v-model="statusFilter"
                                :options="[
                                    { label: 'All', value: '' },
                                    ...statuses.map((s) => ({ label: s.name, value: s.value })),
                                ]"
                                option-label="label"
                                option-value="value"
                                class="w-48"
                            />
                        </div>
                        <Button
                            label="Apply"
                            severity="secondary"
                            size="small"
                            @click="applyFilter"
                        />
                    </section>

                    <!-- Flash message -->
                    <div
                        v-if="successMessage"
                        role="status"
                        aria-live="polite"
                        class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700"
                    >
                        {{ successMessage }}
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200"
                                aria-label="Course list"
                            >
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Code
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Title
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Department
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Sections
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
                                            <span class="sr-only">Change Status</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <tr v-if="courses.data.length === 0">
                                        <td
                                            colspan="6"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No courses found.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="course in courses.data"
                                        :key="course.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td
                                            class="whitespace-nowrap px-6 py-4 text-sm font-mono text-gray-900"
                                        >
                                            {{ course.code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ course.title }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ course.department }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ course.sections.length }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <Tag
                                                :value="course.status"
                                                :severity="getSeverity(course.status)"
                                            />
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <Select
                                                    v-model="pendingStatus[course.id]"
                                                    :options="
                                                        statuses.map((s) => ({
                                                            label: s.name,
                                                            value: s.value,
                                                        }))
                                                    "
                                                    option-label="label"
                                                    option-value="value"
                                                    :placeholder="course.status"
                                                    class="w-36"
                                                    :aria-label="`Change status for ${course.title}`"
                                                />
                                                <Button
                                                    label="Update"
                                                    severity="secondary"
                                                    size="small"
                                                    :aria-label="`Update status for ${course.title}`"
                                                    @click="changeStatus(course)"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav
                        v-if="courses.links && courses.links.length > 3"
                        aria-label="Pagination"
                        class="mt-6 flex justify-center gap-1"
                    >
                        <template v-for="link in courses.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :aria-current="link.active ? 'page' : undefined"
                                class="rounded-md px-3 py-2 text-sm"
                                :class="
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-50'
                                "
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="rounded-md px-3 py-2 text-sm text-gray-400"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
