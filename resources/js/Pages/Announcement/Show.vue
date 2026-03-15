<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Announcement } from '@/Types/models';

const props = defineProps<{
    announcement: Announcement;
    canManage: boolean;
}>();

const publishForm = useForm({});

function togglePublish(): void {
    publishForm.post(route('announcements.publish', props.announcement.id));
}
</script>

<template>
    <Head :title="announcement.title" />

    <main class="mx-auto max-w-3xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
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
                    <h1 class="text-2xl font-bold text-gray-900">{{ announcement.title }}</h1>

                    <div v-if="canManage" class="flex shrink-0 gap-2">
                        <button
                            type="button"
                            class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
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
                            class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        >
                            Edit
                        </Link>
                        <Link
                            :href="route('announcements.destroy', announcement.id)"
                            method="delete"
                            as="button"
                            class="rounded border border-red-300 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-red-600"
                        >
                            Delete
                        </Link>
                    </div>
                </div>

                <p class="mt-2 text-sm text-gray-500">
                    <span
                        :class="
                            announcement.published_at
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-600'
                        "
                        class="mr-2 inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                    >
                        {{ announcement.published_at ? 'Published' : 'Draft' }}
                    </span>
                    By {{ announcement.author?.name }} &middot;
                    {{ new Date(announcement.created_at).toLocaleDateString() }}
                </p>
            </header>

            <div class="prose max-w-none text-gray-800">
                <p class="whitespace-pre-wrap">{{ announcement.body }}</p>
            </div>
        </article>
    </main>
</template>
