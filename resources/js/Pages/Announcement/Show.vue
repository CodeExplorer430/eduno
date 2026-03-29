<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import type { Announcement } from '@/Types/models';

const props = defineProps<{
    announcement: Announcement;
    canManage: boolean;
}>();

const publishForm = useForm({});
const deleteForm = useForm({});
const showDeleteModal = ref(false);

function togglePublish(): void {
    publishForm.post(route('announcements.publish', props.announcement.id));
}

function executeDelete(): void {
    deleteForm.delete(route('announcements.destroy', props.announcement.id));
}
</script>

<template>
    <Head :title="announcement.title" />

    <main class="mx-auto max-w-3xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500 dark:text-slate-400">
                <li>
                    <Link
                        :href="
                            route('sections.announcements.index', announcement.course_section_id)
                        "
                        class="hover:underline"
                    >
                        Announcements
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">{{ announcement.title }}</li>
            </ol>
        </nav>

        <article>
            <header class="mb-6">
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ announcement.title }}
                    </h1>

                    <div v-if="canManage" class="flex shrink-0 gap-2">
                        <button
                            type="button"
                            class="rounded border border-gray-300 dark:border-slate-600 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-700/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                            :aria-label="
                                announcement.published_at
                                    ? 'Unpublish announcement'
                                    : 'Publish announcement'
                            "
                            @click="togglePublish"
                        >
                            {{ announcement.published_at ? 'Unpublish' : 'Publish' }}
                        </button>
                        <Link
                            :href="route('announcements.edit', announcement.id)"
                            class="rounded border border-gray-300 dark:border-slate-600 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-700/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="rounded border border-red-300 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-red-600"
                            @click="showDeleteModal = true"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                    <span
                        :class="
                            announcement.published_at
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-600 dark:text-gray-400 dark:text-slate-500'
                        "
                        class="mr-2 inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                    >
                        {{ announcement.published_at ? 'Published' : 'Draft' }}
                    </span>
                    By {{ announcement.author?.name }} &middot;
                    {{ new Date(announcement.created_at).toLocaleDateString() }}
                </p>
            </header>

            <div class="prose max-w-none text-gray-800 dark:text-gray-200">
                <p class="whitespace-pre-wrap">{{ announcement.body }}</p>
            </div>
        </article>
    </main>

    <Modal
        :show="showDeleteModal"
        max-width="sm"
        labelledby="delete-announcement-title"
        @close="showDeleteModal = false"
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
                    @click="showDeleteModal = false"
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
