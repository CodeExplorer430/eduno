<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { Course } from '@/Types/models';

interface PaginatedCourses {
    data: Course[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

const props = defineProps<{
    courses: PaginatedCourses;
}>();
</script>

<template>
    <Head title="Courses" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">My Courses</h2>
                <Link
                    :href="route('courses.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    aria-label="Create new course"
                >
                    New Course
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    v-if="courses.data.length === 0"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    role="status"
                >
                    <div class="p-6 text-gray-500">No courses found.</div>
                </div>

                <ul v-else class="space-y-4" aria-label="Course list">
                    <li
                        v-for="course in courses.data"
                        :key="course.id"
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <Link
                                        :href="route('courses.show', course.id)"
                                        class="text-lg font-semibold text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline"
                                    >
                                        {{ course.code }} — {{ course.title }}
                                    </Link>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ course.department }} &middot; {{ course.term }}
                                        {{ course.academic_year }}
                                    </p>
                                </div>
                                <span
                                    :class="{
                                        'bg-yellow-100 text-yellow-800': course.status === 'draft',
                                        'bg-green-100 text-green-800':
                                            course.status === 'published',
                                        'bg-gray-100 text-gray-600': course.status === 'archived',
                                    }"
                                    class="rounded-full px-3 py-1 text-xs font-medium capitalize"
                                    :aria-label="`Status: ${course.status}`"
                                >
                                    {{ course.status }}
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>

                <nav
                    v-if="courses.last_page > 1"
                    class="mt-6 flex justify-between"
                    aria-label="Pagination"
                >
                    <Link
                        v-if="courses.prev_page_url"
                        :href="courses.prev_page_url"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Previous
                    </Link>
                    <span class="text-sm text-gray-600">
                        Page {{ courses.current_page }} of {{ courses.last_page }}
                    </span>
                    <Link
                        v-if="courses.next_page_url"
                        :href="courses.next_page_url"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Next
                    </Link>
                </nav>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
