<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface SectionOption {
    id: number;
    section_name: string;
    course: { id: number; code: string; title: string };
}

const props = defineProps<{
    sections: SectionOption[];
    preselectedSection: number | null;
}>();

const form = useForm({
    course_section_id: props.preselectedSection ?? props.sections[0]?.id ?? null,
    title: '',
    body: '',
});

function submit(): void {
    form.post(route('instructor.announcements.store'));
}
</script>

<template>
    <Head title="New Announcement" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'Announcements', href: route('instructor.announcements.index') },
                    { label: 'New Announcement' },
                ]"
            />
        </template>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h1 class="font-semibold text-gray-900 dark:text-white">New Announcement</h1>
                </div>

                <form class="px-6 py-6 space-y-5" novalidate @submit.prevent="submit">
                    <!-- Section picker -->
                    <div>
                        <InputLabel for="course_section_id" value="Section" />
                        <select
                            id="course_section_id"
                            v-model="form.course_section_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            :aria-describedby="
                                form.errors.course_section_id ? 'section-error' : undefined
                            "
                            required
                        >
                            <option :value="null" disabled>Select a section…</option>
                            <optgroup
                                v-for="section in sections"
                                :key="section.id"
                                :label="`${section.course.code} — ${section.course.title}`"
                            >
                                <option :value="section.id">{{ section.section_name }}</option>
                            </optgroup>
                        </select>
                        <InputError
                            id="section-error"
                            class="mt-1"
                            :message="form.errors.course_section_id"
                        />
                    </div>

                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            maxlength="255"
                            :aria-describedby="form.errors.title ? 'title-error' : undefined"
                            required
                        />
                        <InputError id="title-error" class="mt-1" :message="form.errors.title" />
                    </div>

                    <!-- Body -->
                    <div>
                        <InputLabel for="body" value="Body" />
                        <textarea
                            id="body"
                            v-model="form.body"
                            rows="6"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            :aria-describedby="form.errors.body ? 'body-error' : undefined"
                            required
                        ></textarea>
                        <InputError id="body-error" class="mt-1" :message="form.errors.body" />
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-slate-700"
                    >
                        <a
                            :href="route('instructor.announcements.index')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :aria-busy="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                        >
                            <span v-if="form.processing">Saving…</span>
                            <span v-else>Publish Announcement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
