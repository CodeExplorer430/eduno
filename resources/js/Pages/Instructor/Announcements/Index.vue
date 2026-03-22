<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PlusIcon, MegaphoneIcon } from '@heroicons/vue/24/outline';

interface Announcement {
    id: number;
    title: string;
    body: string;
    published_at: string | null;
    course_section?: { section_name: string; course?: { title: string } };
}

defineProps<{ announcements: Announcement[] }>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));

const deleteForm = useForm({});
const confirmTarget = ref<number | null>(null);

const requestDelete = (id: number): void => {
    confirmTarget.value = id;
};
const confirmDelete = (): void => {
    if (confirmTarget.value === null) return;
    deleteForm.delete(route('instructor.announcements.destroy', confirmTarget.value));
    confirmTarget.value = null;
};
const cancelDelete = (): void => {
    confirmTarget.value = null;
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Announcements</h1>
                <Link
                    :href="route('instructor.announcements.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    New Announcement
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <section aria-labelledby="announcements-heading">
                <h2 id="announcements-heading" class="sr-only">Announcements list</h2>

                <div
                    v-if="announcements.length === 0"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                >
                    <MegaphoneIcon
                        class="mx-auto mb-3 h-10 w-10 text-gray-300"
                        aria-hidden="true"
                    />
                    <p class="text-sm text-gray-500">
                        No announcements yet. Create one to notify your students.
                    </p>
                </div>

                <ul v-else class="space-y-4" aria-label="Announcements">
                    <li
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <article :aria-labelledby="`announcement-${announcement.id}-title`">
                            <div class="px-6 py-5">
                                <header class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50"
                                        >
                                            <MegaphoneIcon
                                                class="h-4 w-4 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div>
                                            <h3
                                                :id="`announcement-${announcement.id}-title`"
                                                class="text-base font-semibold text-gray-900"
                                            >
                                                {{ announcement.title }}
                                            </h3>
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                <span class="font-medium text-blue-600">
                                                    {{ announcement.course_section?.course?.title }}
                                                </span>
                                                — {{ announcement.course_section?.section_name }}
                                                <span v-if="announcement.published_at">
                                                    &middot;
                                                    <time :datetime="announcement.published_at">
                                                        {{ formatDate(announcement.published_at) }}
                                                    </time>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 gap-2">
                                        <Link
                                            :href="
                                                route(
                                                    'instructor.announcements.edit',
                                                    announcement.id
                                                )
                                            "
                                            class="rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :aria-label="`Edit ${announcement.title}`"
                                        >
                                            Edit
                                        </Link>
                                        <template v-if="confirmTarget === announcement.id">
                                            <span class="text-sm text-gray-600">Delete?</span>
                                            <button
                                                type="button"
                                                class="rounded text-sm font-medium text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                :aria-busy="deleteForm.processing"
                                                @click="confirmDelete"
                                            >
                                                Yes
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                @click="cancelDelete"
                                            >
                                                No
                                            </button>
                                        </template>
                                        <button
                                            v-else
                                            type="button"
                                            class="rounded text-sm font-medium text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                            :aria-label="`Delete ${announcement.title}`"
                                            @click="requestDelete(announcement.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </header>
                                <p class="mt-3 line-clamp-2 text-sm text-gray-600">
                                    {{ announcement.body }}
                                </p>
                            </div>
                        </article>
                    </li>
                </ul>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
