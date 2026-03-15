<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Announcement, CourseSection } from '@/Types/models';

defineProps<{
    section: CourseSection;
    announcements: Announcement[];
    canManage: boolean;
}>();

const publishForm = useForm({});

function togglePublish(announcement: Announcement): void {
    publishForm.post(route('announcements.publish', announcement.id));
}
</script>

<template>
    <Head :title="`Announcements — ${section.section_name}`" />

    <main class="mx-auto max-w-4xl px-4 py-8">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <nav aria-label="Breadcrumb">
                    <ol class="flex gap-2 text-sm text-gray-500">
                        <li>
                            <Link :href="route('courses.index')" class="hover:underline"
                                >Courses</Link
                            >
                        </li>
                        <li aria-hidden="true">/</li>
                        <li>
                            <Link
                                :href="route('sections.show', section.id)"
                                class="hover:underline"
                            >
                                {{ section.section_name }}
                            </Link>
                        </li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page">Announcements</li>
                    </ol>
                </nav>
                <h1 class="mt-1 text-2xl font-bold">Announcements</h1>
            </div>

            <Link
                v-if="canManage"
                :href="route('sections.announcements.create', section.id)"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                New Announcement
            </Link>
        </header>

        <div
            v-if="announcements.length === 0"
            class="rounded border border-dashed border-gray-300 px-6 py-12 text-center text-gray-500"
            role="status"
        >
            No announcements yet.
        </div>

        <ul v-else class="space-y-4" role="list">
            <li
                v-for="announcement in announcements"
                :key="announcement.id"
                class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <Link
                                :href="route('announcements.show', announcement.id)"
                                class="text-lg font-semibold text-gray-900 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            >
                                {{ announcement.title }}
                            </Link>
                            <span
                                :class="
                                    announcement.published_at
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-600'
                                "
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :aria-label="announcement.published_at ? 'Published' : 'Draft'"
                            >
                                {{ announcement.published_at ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            By {{ announcement.author?.name }} &middot;
                            {{ new Date(announcement.created_at).toLocaleDateString() }}
                        </p>
                    </div>

                    <div v-if="canManage" class="flex shrink-0 gap-2">
                        <button
                            type="button"
                            class="text-sm text-blue-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            :aria-label="`${announcement.published_at ? 'Unpublish' : 'Publish'} ${announcement.title}`"
                            @click="togglePublish(announcement)"
                        >
                            {{ announcement.published_at ? 'Unpublish' : 'Publish' }}
                        </button>

                        <Link
                            :href="route('announcements.edit', announcement.id)"
                            class="text-sm text-gray-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            :aria-label="`Edit ${announcement.title}`"
                        >
                            Edit
                        </Link>

                        <Link
                            :href="route('announcements.destroy', announcement.id)"
                            method="delete"
                            as="button"
                            class="text-sm text-red-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-red-600"
                            :aria-label="`Delete ${announcement.title}`"
                        >
                            Delete
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </main>
</template>
