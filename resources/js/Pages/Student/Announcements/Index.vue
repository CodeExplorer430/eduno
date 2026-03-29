<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';
import { MegaphoneIcon } from '@heroicons/vue/24/outline';

interface Course {
    id: number;
    code: string;
    title: string;
}

interface CourseSection {
    id: number;
    section_name: string;
    course: Course;
}

interface Author {
    id: number;
    name: string;
}

interface AnnouncementItem {
    id: number;
    title: string;
    body: string;
    published_at: string;
    course_section: CourseSection;
    author: Author;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated {
    data: AnnouncementItem[];
    links: PaginationLink[];
    meta?: {
        current_page: number;
        last_page: number;
        total: number;
    };
    current_page?: number;
    last_page?: number;
    total?: number;
}

const props = defineProps<{
    announcements: Paginated;
}>();

const courseFilter = ref<number | null>(null);

const allCourses = computed<Course[]>(() => {
    const seen = new Set<number>();
    const result: Course[] = [];
    for (const a of props.announcements.data) {
        const c = a.course_section.course;
        if (!seen.has(c.id)) {
            seen.add(c.id);
            result.push(c);
        }
    }
    return result;
});

const filtered = computed<AnnouncementItem[]>(() => {
    if (!courseFilter.value) return props.announcements.data;
    return props.announcements.data.filter(
        (a) => a.course_section.course.id === courseFilter.value
    );
});

const formatDate = (iso: string): string => {
    return new Date(iso).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Announcements
            </h1>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Course filter -->
            <div v-if="allCourses.length > 1" class="mb-6">
                <label for="course-filter" class="sr-only">Filter by course</label>
                <select
                    id="course-filter"
                    v-model="courseFilter"
                    class="block w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-auto"
                >
                    <option :value="null">All Courses</option>
                    <option v-for="course in allCourses" :key="course.id" :value="course.id">
                        {{ course.code }} — {{ course.title }}
                    </option>
                </select>
            </div>

            <div
                v-if="filtered.length > 0"
                class="space-y-4"
                role="feed"
                aria-label="Course announcements"
            >
                <article
                    v-for="announcement in filtered"
                    :key="announcement.id"
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 dark:bg-slate-800 ring-gray-100 dark:ring-slate-700"
                    :aria-labelledby="`announcement-title-${announcement.id}`"
                >
                    <header class="border-b border-gray-100 dark:border-slate-700 px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50"
                            >
                                <MegaphoneIcon class="h-4 w-4 text-blue-600" aria-hidden="true" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <h2
                                        :id="`announcement-title-${announcement.id}`"
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ announcement.title }}
                                    </h2>
                                    <time
                                        :datetime="announcement.published_at"
                                        class="shrink-0 text-xs text-gray-400 dark:text-slate-500"
                                    >
                                        {{ formatDate(announcement.published_at) }}
                                    </time>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                    <span class="font-medium text-blue-600">
                                        {{ announcement.course_section.course.code }}
                                    </span>
                                    &mdash;
                                    {{ announcement.course_section.course.title }}
                                    &bull;
                                    {{ announcement.author.name }}
                                </p>
                            </div>
                        </div>
                    </header>
                    <div class="px-6 py-4">
                        <p class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">
                            {{ announcement.body }}
                        </p>
                    </div>
                </article>
            </div>

            <EmptyState
                v-else
                :icon="MegaphoneIcon"
                title="No announcements yet."
                description="Announcements from your enrolled courses will appear here."
            />

            <Pagination
                v-if="announcements.links && announcements.links.length > 3"
                :links="announcements.links"
                class="mt-6"
            />
        </div>
    </AuthenticatedLayout>
</template>
