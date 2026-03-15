<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Course, CourseSection, Lesson, Module } from '@/Types/models';

interface ModuleWithRelations extends Module {
    section: CourseSection & { course: Course };
    lessons?: Lesson[];
}

const props = defineProps<{
    module: ModuleWithRelations;
    lessons: Lesson[];
    canManage: boolean;
}>();

const publishForm = useForm({});

function togglePublish(): void {
    publishForm.post(route('modules.publish', props.module.id));
}

function destroyLesson(lessonId: number): void {
    if (confirm('Delete this lesson? This cannot be undone.')) {
        publishForm.delete(route('lessons.destroy', lessonId));
    }
}

function togglePublishLesson(lessonId: number): void {
    publishForm.post(route('lessons.publish', lessonId));
}
</script>

<template>
    <Head :title="module.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start justify-between">
                <div>
                    <nav aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2 text-sm text-gray-500">
                            <li>
                                <Link
                                    :href="route('courses.show', module.section.course_id)"
                                    class="hover:text-gray-700 focus:underline focus:outline-none"
                                >
                                    {{ module.section.course?.title ?? 'Course' }}
                                </Link>
                            </li>
                            <li aria-hidden="true">/</li>
                            <li>
                                <Link
                                    :href="
                                        route('sections.modules.index', module.course_section_id)
                                    "
                                    class="hover:text-gray-700 focus:underline focus:outline-none"
                                >
                                    Modules
                                </Link>
                            </li>
                            <li aria-hidden="true">/</li>
                            <li class="font-medium text-gray-800" aria-current="page">
                                {{ module.title }}
                            </li>
                        </ol>
                    </nav>
                    <div class="mt-1 flex items-center gap-2">
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            {{ module.title }}
                        </h2>
                        <span
                            v-if="!module.published_at"
                            class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                            role="status"
                        >
                            Draft
                        </span>
                    </div>
                </div>

                <div v-if="canManage" class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :aria-label="module.published_at ? 'Unpublish module' : 'Publish module'"
                        @click="togglePublish"
                    >
                        {{ module.published_at ? 'Unpublish' : 'Publish' }}
                    </button>
                    <Link
                        :href="route('modules.edit', module.id)"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        aria-label="Edit module"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <div
                    v-if="module.description"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-sm text-gray-700">
                        {{ module.description }}
                    </div>
                </div>

                <section aria-labelledby="lessons-heading">
                    <div class="flex items-center justify-between">
                        <h3 id="lessons-heading" class="text-lg font-semibold text-gray-800">
                            Lessons
                        </h3>
                        <Link
                            v-if="canManage"
                            :href="route('modules.lessons.create', module.id)"
                            class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            aria-label="Add new lesson"
                        >
                            Add Lesson
                        </Link>
                    </div>

                    <div
                        v-if="lessons.length === 0"
                        class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6 text-sm text-gray-500">No lessons yet.</div>
                    </div>

                    <ol v-else class="mt-4 space-y-3">
                        <li
                            v-for="lesson in lessons"
                            :key="lesson.id"
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="flex items-center justify-between p-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400"
                                            >{{ lesson.order_no }}.</span
                                        >
                                        <Link
                                            :href="route('lessons.show', lesson.id)"
                                            class="font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline"
                                        >
                                            {{ lesson.title }}
                                        </Link>
                                        <span
                                            class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium uppercase text-gray-600"
                                        >
                                            {{ lesson.type }}
                                        </span>
                                        <span
                                            v-if="!lesson.published_at"
                                            class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                                            role="status"
                                        >
                                            Draft
                                        </span>
                                    </div>
                                </div>

                                <div v-if="canManage" class="ml-4 flex shrink-0 gap-2">
                                    <button
                                        type="button"
                                        class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :aria-label="
                                            lesson.published_at
                                                ? `Unpublish ${lesson.title}`
                                                : `Publish ${lesson.title}`
                                        "
                                        @click="togglePublishLesson(lesson.id)"
                                    >
                                        {{ lesson.published_at ? 'Unpublish' : 'Publish' }}
                                    </button>
                                    <Link
                                        :href="route('lessons.edit', lesson.id)"
                                        class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :aria-label="`Edit ${lesson.title}`"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="rounded border border-red-300 bg-white px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Delete ${lesson.title}`"
                                        @click="destroyLesson(lesson.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ol>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
