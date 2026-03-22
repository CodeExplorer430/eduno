<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
                <Link :href="route('instructor.announcements.create')">
                    <Button type="button">New Announcement</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="announcements-heading">
                        <h2 id="announcements-heading" class="sr-only">Announcements list</h2>

                        <div
                            v-if="announcements.length === 0"
                            role="status"
                            class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                        >
                            <p class="text-sm text-gray-500">
                                No announcements yet. Create one to notify your students.
                            </p>
                        </div>

                        <ul v-else class="space-y-4" aria-label="Announcements">
                            <li
                                v-for="announcement in announcements"
                                :key="announcement.id"
                                class="overflow-hidden rounded-lg bg-white shadow-sm"
                            >
                                <article :aria-labelledby="`announcement-${announcement.id}-title`">
                                    <div class="px-6 py-4">
                                        <header class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3
                                                    :id="`announcement-${announcement.id}-title`"
                                                    class="text-base font-semibold text-gray-900"
                                                >
                                                    {{ announcement.title }}
                                                </h3>
                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    {{ announcement.course_section?.course?.title }}
                                                    &mdash;
                                                    {{ announcement.course_section?.section_name }}
                                                    <span v-if="announcement.published_at">
                                                        &middot;
                                                        <time :datetime="announcement.published_at">
                                                            {{
                                                                formatDate(
                                                                    announcement.published_at
                                                                )
                                                            }}
                                                        </time>
                                                    </span>
                                                </p>
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
                                                <form
                                                    @submit.prevent="requestDelete(announcement.id)"
                                                >
                                                    <Button
                                                        type="submit"
                                                        severity="danger"
                                                        size="small"
                                                        :aria-label="`Delete ${announcement.title}`"
                                                    >
                                                        Delete
                                                    </Button>
                                                </form>
                                            </div>
                                        </header>
                                        <p class="mt-2 line-clamp-2 text-sm text-gray-600">
                                            {{ announcement.body }}
                                        </p>
                                    </div>
                                </article>
                            </li>
                        </ul>
                    </section>
                </main>
            </div>
        </div>

        <Dialog
            :visible="confirmTarget !== null"
            modal
            header="Delete Announcement"
            :closable="false"
            @update:visible="cancelDelete"
        >
            <p id="confirm-delete-desc">
                Are you sure you want to delete this announcement? This action cannot be undone.
            </p>
            <template #footer>
                <Button severity="secondary" @click="cancelDelete">Cancel</Button>
                <Button
                    severity="danger"
                    :disabled="deleteForm.processing"
                    :aria-busy="deleteForm.processing"
                    @click="confirmDelete"
                >
                    <span v-if="deleteForm.processing">Deleting&hellip;</span>
                    <span v-else>Delete</span>
                </Button>
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
