<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Course, CourseSection, Module, PaginatedResponse } from '@/Types/models';

interface SectionWithCourse extends CourseSection {
    course: Course;
}

const props = defineProps<{
    section: SectionWithCourse;
    modules: PaginatedResponse<Module>;
    canManage: boolean;
}>();

const publishForm = useForm({});

function togglePublish(moduleId: number): void {
    publishForm.post(route('modules.publish', moduleId));
}

function destroyModule(moduleId: number): void {
    if (confirm('Delete this module? This cannot be undone.')) {
        publishForm.delete(route('modules.destroy', moduleId));
    }
}
</script>

<template>
    <Head title="Modules" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <nav aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2 text-sm text-gray-500">
                            <li>
                                <Link
                                    :href="route('courses.show', section.course_id)"
                                    class="hover:text-gray-700 focus:underline focus:outline-none"
                                >
                                    {{ section.course?.title ?? 'Course' }}
                                </Link>
                            </li>
                            <li aria-hidden="true">/</li>
                            <li class="font-medium text-gray-800" aria-current="page">
                                {{ section.section_name }} — Modules
                            </li>
                        </ol>
                    </nav>
                    <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">Modules</h2>
                </div>
                <Link
                    v-if="canManage"
                    :href="route('sections.modules.create', section.id)"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    aria-label="Add new module"
                >
                    Add Module
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div
                    v-if="modules.data.length === 0"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    role="status"
                >
                    <div class="p-6 text-gray-500">
                        No modules yet.
                        <span v-if="canManage">
                            <Link
                                :href="route('sections.modules.create', section.id)"
                                class="text-blue-600 hover:underline focus:outline-none focus:underline"
                            >
                                Create the first module.
                            </Link>
                        </span>
                    </div>
                </div>

                <ol v-else class="space-y-3" aria-label="Module list">
                    <li
                        v-for="module in modules.data"
                        :key="module.id"
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="flex items-center justify-between p-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-400">
                                        {{ module.order_no }}.
                                    </span>
                                    <Link
                                        :href="route('modules.show', module.id)"
                                        class="font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:underline"
                                    >
                                        {{ module.title }}
                                    </Link>
                                    <span
                                        v-if="!module.published_at"
                                        class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                                        role="status"
                                    >
                                        Draft
                                    </span>
                                </div>
                                <p
                                    v-if="module.description"
                                    class="ml-5 mt-0.5 text-sm text-gray-500"
                                >
                                    {{ module.description }}
                                </p>
                            </div>

                            <div v-if="canManage" class="ml-4 flex shrink-0 gap-2">
                                <button
                                    type="button"
                                    class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :aria-label="
                                        module.published_at
                                            ? `Unpublish ${module.title}`
                                            : `Publish ${module.title}`
                                    "
                                    @click="togglePublish(module.id)"
                                >
                                    {{ module.published_at ? 'Unpublish' : 'Publish' }}
                                </button>
                                <Link
                                    :href="route('modules.edit', module.id)"
                                    class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :aria-label="`Edit ${module.title}`"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    class="rounded border border-red-300 bg-white px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    :aria-label="`Delete ${module.title}`"
                                    @click="destroyModule(module.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </li>
                </ol>

                <Pagination v-if="modules.links.length > 3" :links="modules.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
