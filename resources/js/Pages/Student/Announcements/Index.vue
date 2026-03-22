<script setup lang="ts">
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
            <h1 class="text-xl font-semibold leading-tight text-gray-800">Announcements</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <main>
                    <div
                        v-if="announcements.data.length > 0"
                        class="space-y-4"
                        role="feed"
                        aria-label="Course announcements"
                    >
                        <article
                            v-for="announcement in announcements.data"
                            :key="announcement.id"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                            :aria-labelledby="`announcement-title-${announcement.id}`"
                        >
                            <header class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <h2
                                        :id="`announcement-title-${announcement.id}`"
                                        class="font-semibold text-gray-900"
                                    >
                                        {{ announcement.title }}
                                    </h2>
                                    <time
                                        :datetime="announcement.published_at"
                                        class="shrink-0 text-xs text-gray-400"
                                    >
                                        {{ formatDate(announcement.published_at) }}
                                    </time>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    <span class="font-medium text-blue-600">
                                        {{ announcement.course_section.course.code }}
                                    </span>
                                    &mdash;
                                    {{ announcement.course_section.course.title }}
                                    &bull;
                                    {{ announcement.author.name }}
                                </p>
                            </header>

                            <div class="px-6 py-4">
                                <p class="whitespace-pre-wrap text-sm text-gray-700">
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
                    />
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
