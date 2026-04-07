<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import type { Announcement, CourseSection, PaginatedResponse } from '@/Types/models';

const props = defineProps<{
    section: CourseSection;
    announcements: PaginatedResponse<Announcement>;
    canManage: boolean;
}>();

const publishForm = useForm({});
const deleteForm = useForm({});
const confirmDeleteId = ref<number | null>(null);

function togglePublish(announcement: Announcement): void {
    publishForm.post(route('announcements.publish', announcement.id));
}

function confirmDelete(id: number): void {
    confirmDeleteId.value = id;
}

function executeDelete(): void {
    if (confirmDeleteId.value === null) return;
    deleteForm.delete(route('announcements.destroy', confirmDeleteId.value), {
        onSuccess: () => {
            confirmDeleteId.value = null;
        },
    });
}
</script>

<template>
    <Head :title="`Announcements — ${section.section_name}`" />

    <main class="mx-auto max-w-4xl px-4 py-8">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <nav aria-label="Breadcrumb">
                    <ol class="flex gap-2 text-sm text-gray-500 dark:text-slate-400">
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
            v-if="announcements.data.length === 0"
            class="rounded border border-dashed border-gray-300 dark:border-slate-600 px-6 py-12 text-center text-gray-500 dark:text-slate-400"
            role="status"
        >
            No announcements yet.
        </div>

        <ul v-else class="space-y-4" role="list">
            <li
                v-for="announcement in announcements.data"
                :key="announcement.id"
                class="rounded-lg border border-gray-200 dark:border-slate-600 bg-white p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <Link
                                :href="route('announcements.show', announcement.id)"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            >
                                {{ announcement.title }}
                            </Link>
                            <span
                                :class="
                                    announcement.published_at
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-600 dark:text-gray-400 dark:text-slate-500'
                                "
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :aria-label="announcement.published_at ? 'Published' : 'Draft'"
                            >
                                {{ announcement.published_at ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
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
                            class="text-sm text-gray-600 dark:text-gray-400 dark:text-slate-500 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            :aria-label="`Edit ${announcement.title}`"
                        >
                            Edit
                        </Link>

                        <button
                            type="button"
                            class="text-sm text-red-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-red-600"
                            :aria-label="`Delete ${announcement.title}`"
                            @click="confirmDelete(announcement.id)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </li>
        </ul>

        <Pagination v-if="announcements.links.length > 3" :links="announcements.links" />
    </main>

    <Modal
        :show="confirmDeleteId !== null"
        max-width="sm"
        labelledby="delete-announcement-title"
        @close="confirmDeleteId = null"
    >
        <div class="p-6">
            <h2
                id="delete-announcement-title"
                class="text-lg font-semibold text-gray-900 dark:text-white"
            >
                Delete Announcement?
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 dark:text-slate-500">
                This will permanently delete the announcement.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="rounded-md border border-gray-300 dark:border-slate-600 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    @click="confirmDeleteId = null"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="deleteForm.processing"
                    class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 disabled:opacity-50"
                    @click="executeDelete"
                >
                    Delete
                </button>
            </div>
        </div>
    </Modal>
</template>
