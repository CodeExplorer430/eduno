<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
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

const statusBadge: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400',
    published: 'bg-green-100 text-green-700',
    archived: 'bg-amber-100 text-amber-700',
};
</script>

<template>
    <Head title="My Courses — Instructor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Courses</h1>
                <Link
                    :href="route('instructor.courses.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    Create Course
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Stats strip -->
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Total Courses
                    </p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ courses.length }}
                    </p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600">
                        Published
                    </p>
                    <p class="mt-1 text-2xl font-bold text-green-700">
                        {{ courses.filter((c) => c.status === 'published').length }}
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
                        {{ courses.filter((c) => c.status === 'draft').length }}
                    </p>
                </div>
            </div>

            <section aria-labelledby="courses-heading">
                <h2 id="courses-heading" class="sr-only">Courses list</h2>

                <EmptyState
                    v-if="courses.length === 0"
                    :icon="BookOpenIcon"
                    title="No courses yet."
                    description="Create your first course to get started."
                >
                    <Link
                        :href="route('instructor.courses.create')"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <PlusIcon class="h-4 w-4" aria-hidden="true" />
                        Create Course
                    </Link>
                </EmptyState>

                <div
                    v-else
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3"
                    role="list"
                    aria-label="My courses"
                >
                    <article
                        v-for="course in courses"
                        :key="course.id"
                        role="listitem"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:shadow-md dark:bg-slate-800 dark:ring-slate-700"
                        :aria-labelledby="`course-${course.id}-title`"
                    >
                        <div class="h-1 bg-gradient-to-r from-blue-500 to-cyan-400"></div>
                        <div class="p-5">
                            <header class="mb-3">
                                <div class="mb-1 flex items-center justify-between gap-2">
                                    <span
                                        class="inline-block rounded bg-gray-100 px-1.5 py-0.5 font-mono text-xs text-gray-600 dark:bg-slate-700 dark:text-gray-400"
                                    >
                                        {{ course.code }}
                                    </span>
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            statusBadge[course.status] ??
                                            'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-gray-400'
                                        "
                                    >
                                        {{ course.status }}
                                    </span>
                                </div>
                                <h3
                                    :id="`course-${course.id}-title`"
                                    class="text-base font-semibold leading-snug text-gray-900 dark:text-white"
                                >
                                    {{ course.title }}
                                </h3>
                            </header>
                            <p class="text-sm text-gray-500 dark:text-slate-400">
                                {{ course.sections.length }}
                                {{ course.sections.length === 1 ? 'section' : 'sections' }}
                            </p>
                        </div>
                        <footer
                            class="flex items-center gap-4 border-t border-gray-100 px-5 py-3 text-sm dark:border-slate-700"
                        >
                            <template v-for="section in course.sections" :key="section.id">
                                <Link
                                    :href="route('instructor.courses.modules.index', section.id)"
                                    class="inline-flex items-center gap-1 rounded font-medium text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:text-white"
                                    :aria-label="`Manage modules for ${section.section_name}`"
                                >
                                    <RectangleStackIcon class="h-4 w-4" aria-hidden="true" />
                                    {{ section.section_name }}
                                </Link>
                            </template>
                            <Link
                                :href="route('instructor.courses.edit', course.id)"
                                class="inline-flex items-center gap-1 rounded font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-label="`Edit course ${course.title}`"
                            >
                                <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                                Edit
                            </Link>
                        </footer>
                    </article>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
