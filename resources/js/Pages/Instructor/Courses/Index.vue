<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Section {
    id: number;
    section_name: string;
    course_id: number;
}

interface Course {
    id: number;
    code: string;
    title: string;
    status: string;
    sections: Section[];
}

defineProps<{
    courses: Course[];
}>();

const validStatuses = ['submitted', 'graded', 'returned', 'late', 'pending'] as const;
type ValidStatus = (typeof validStatuses)[number];

const safeStatus = (s: string): ValidStatus =>
    validStatuses.includes(s as ValidStatus) ? (s as ValidStatus) : 'pending';
</script>

<template>
    <Head title="My Courses — Instructor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">My Courses</h1>
                <Link
                    :href="route('instructor.courses.create')"
                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Create Course
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="courses-table-heading">
                        <h2 id="courses-table-heading" class="sr-only">Courses table</h2>

                        <div
                            v-if="courses.length > 0"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                    aria-label="Instructor courses"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                            >
                                                Code
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                            >
                                                Title
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
                                                Sections
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
                                        <tr
                                            v-for="course in courses"
                                            :key="course.id"
                                            class="transition hover:bg-gray-50"
                                        >
                                            <td
                                                class="px-6 py-4 text-sm font-mono font-medium text-gray-900"
                                            >
                                                {{ course.code }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                {{ course.title }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <StatusBadge :variant="safeStatus(course.status)" />
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ course.sections.length }}
                                                {{
                                                    course.sections.length === 1
                                                        ? 'section'
                                                        : 'sections'
                                                }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm">
                                                <div class="flex items-center justify-end gap-4">
                                                    <template
                                                        v-for="section in course.sections"
                                                        :key="section.id"
                                                    >
                                                        <Link
                                                            :href="
                                                                route(
                                                                    'instructor.courses.modules.index',
                                                                    section.id
                                                                )
                                                            "
                                                            class="font-medium text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                                                            :aria-label="`Manage modules for ${course.title} — ${section.section_name}`"
                                                        >
                                                            Modules
                                                        </Link>
                                                    </template>
                                                    <Link
                                                        :href="
                                                            route(
                                                                'instructor.courses.edit',
                                                                course.id
                                                            )
                                                        "
                                                        class="font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                                                        :aria-label="`Edit course ${course.title}`"
                                                    >
                                                        Edit
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div
                            v-else
                            class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white py-20 text-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mb-4 h-12 w-12 text-gray-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                                />
                            </svg>
                            <p class="text-base font-medium text-gray-500">No courses yet.</p>
                            <p class="mt-1 text-sm text-gray-400">
                                Create your first course to get started.
                            </p>
                            <Link
                                :href="route('instructor.courses.create')"
                                class="mt-4 inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Create Course
                            </Link>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
