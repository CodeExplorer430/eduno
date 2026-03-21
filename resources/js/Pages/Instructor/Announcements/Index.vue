<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
const deleteAnnouncement = (id: number): void => {
    if (!confirm('Delete this announcement?')) return;
    deleteForm.delete(route('instructor.announcements.destroy', id));
};
</script>

<template>
    <Head title="Announcements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">Announcements</h1>
                <Link :href="route('instructor.announcements.create')">
                    <PrimaryButton type="button">New Announcement</PrimaryButton>
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
                                                    class="rounded text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    :aria-label="`Edit ${announcement.title}`"
                                                >
                                                    Edit
                                                </Link>
                                                <form
                                                    @submit.prevent="
                                                        deleteAnnouncement(announcement.id)
                                                    "
                                                >
                                                    <DangerButton
                                                        type="submit"
                                                        class="text-xs px-2 py-1"
                                                        :aria-label="`Delete ${announcement.title}`"
                                                    >
                                                        Delete
                                                    </DangerButton>
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
    </AuthenticatedLayout>
</template>
