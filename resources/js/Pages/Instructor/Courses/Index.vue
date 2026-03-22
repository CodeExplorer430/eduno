<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Tag from 'primevue/tag';
import { Head, Link } from '@inertiajs/vue3';
import {
    PlusIcon,
    RectangleStackIcon,
    PencilSquareIcon,
    BookOpenIcon,
} from '@heroicons/vue/24/outline';

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

const statusSeverity: Record<string, 'info' | 'success' | 'secondary' | 'danger' | 'warn'> = {
    submitted: 'info',
    graded: 'success',
    returned: 'secondary',
    late: 'danger',
    pending: 'warn',
};
</script>

<template>
    <Head title="My Courses — Instructor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">My Courses</h1>
                <Link
                    :href="route('instructor.courses.create')"
                    class="inline-flex items-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
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
                                                <Tag
                                                    :severity="
                                                        statusSeverity[course.status] ?? 'warn'
                                                    "
                                                    :value="course.status"
                                                    class="capitalize"
                                                />
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
                                                            class="inline-flex items-center font-medium text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                                                            :aria-label="`Manage modules for ${course.title} — ${section.section_name}`"
                                                        >
                                                            <RectangleStackIcon
                                                                class="me-1 inline h-4 w-4"
                                                                aria-hidden="true"
                                                            />
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
                                                        class="inline-flex items-center font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                                                        :aria-label="`Edit course ${course.title}`"
                                                    >
                                                        <PencilSquareIcon
                                                            class="me-1 inline h-4 w-4"
                                                            aria-hidden="true"
                                                        />
                                                        Edit
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <EmptyState
                            v-else
                            :icon="BookOpenIcon"
                            title="No courses yet."
                            description="Create your first course to get started."
                        >
                            <Link
                                :href="route('instructor.courses.create')"
                                class="mt-4 inline-flex items-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <PlusIcon class="h-4 w-4" aria-hidden="true" />
                                Create Course
                            </Link>
                        </EmptyState>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
