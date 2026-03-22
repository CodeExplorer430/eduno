<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PlusIcon, PencilSquareIcon, TrashIcon, BookOpenIcon } from '@heroicons/vue/24/outline';

interface ResourceItem {
    id: number;
    title: string;
    mime_type: string;
    size_bytes: number;
}

interface LessonItem {
    id: number;
    title: string;
    type: string;
    order_no: number;
    published_at: string | null;
    resources: ResourceItem[];
}

interface ModuleItem {
    id: number;
    title: string;
    description: string | null;
    order_no: number;
    published_at: string | null;
    lessons: LessonItem[];
}

interface Section {
    id: number;
    section_name: string;
    course: { id: number; code: string; title: string };
    modules: ModuleItem[];
}

const props = defineProps<{
    section: Section;
}>();

interface ConfirmState {
    visible: boolean;
    message: string;
    onConfirm: () => void;
}

const confirm = ref<ConfirmState>({ visible: false, message: '', onConfirm: () => {} });

function openConfirm(message: string, action: () => void): void {
    confirm.value = { visible: true, message, onConfirm: action };
}

function closeConfirm(): void {
    confirm.value = { visible: false, message: '', onConfirm: () => {} };
}

function executeConfirm(): void {
    confirm.value.onConfirm();
    closeConfirm();
}

const deleteModule = (moduleId: number): void => {
    openConfirm('Delete this module and all its lessons?', () => {
        router.delete(
            route('instructor.courses.modules.destroy', {
                section: props.section.id,
                module: moduleId,
            })
        );
    });
};

const deleteLesson = (moduleId: number, lessonId: number): void => {
    openConfirm('Delete this lesson?', () => {
        router.delete(
            route('instructor.courses.modules.lessons.destroy', {
                section: props.section.id,
                module: moduleId,
                lesson: lessonId,
            })
        );
    });
};

const deleteResource = (moduleId: number, lessonId: number, resourceId: number): void => {
    openConfirm('Delete this resource file?', () => {
        router.delete(
            route('instructor.courses.modules.lessons.resources.destroy', {
                section: props.section.id,
                module: moduleId,
                lesson: lessonId,
                resource: resourceId,
            })
        );
    });
};
</script>

<template>
    <Head :title="`Modules — ${section.course.code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <Breadcrumb
                    :crumbs="[
                        { label: 'My Courses', href: route('instructor.courses.index') },
                        { label: `${section.course.code} — Modules` },
                    ]"
                />
                <Link
                    :href="route('instructor.courses.modules.create', section.id)"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    Add Module
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <EmptyState
                v-if="section.modules.length === 0"
                :icon="BookOpenIcon"
                title="No modules yet."
                description="Create your first module to get started."
            >
                <Link
                    :href="route('instructor.courses.modules.create', section.id)"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    Add Module
                </Link>
            </EmptyState>

            <div v-else class="space-y-5">
                <article
                    v-for="module in section.modules"
                    :key="module.id"
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    :aria-labelledby="`module-title-${module.id}`"
                >
                    <header
                        class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                            >
                                {{ module.order_no }}
                            </span>
                            <div>
                                <h2
                                    :id="`module-title-${module.id}`"
                                    class="font-semibold text-gray-900"
                                >
                                    {{ module.title }}
                                </h2>
                                <span
                                    v-if="module.published_at"
                                    class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                >
                                    Published
                                </span>
                                <span
                                    v-else
                                    class="inline-block rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-700"
                                >
                                    Draft
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <Link
                                :href="
                                    route('instructor.courses.modules.lessons.create', {
                                        section: section.id,
                                        module: module.id,
                                    })
                                "
                                class="inline-flex items-center gap-1 rounded text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-label="`Add lesson to ${module.title}`"
                            >
                                <PlusIcon class="h-4 w-4" aria-hidden="true" />
                                Lesson
                            </Link>
                            <Link
                                :href="
                                    route('instructor.courses.modules.edit', {
                                        section: section.id,
                                        module: module.id,
                                    })
                                "
                                class="inline-flex items-center gap-1 rounded text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-label="`Edit module ${module.title}`"
                            >
                                <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                                Edit
                            </Link>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                :aria-label="`Delete module ${module.title}`"
                                @click="deleteModule(module.id)"
                            >
                                <TrashIcon class="h-4 w-4" aria-hidden="true" />
                                Delete
                            </button>
                        </div>
                    </header>

                    <div v-if="module.lessons.length > 0" class="divide-y divide-gray-100">
                        <div v-for="lesson in module.lessons" :key="lesson.id" class="px-5 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-800">{{
                                        lesson.title
                                    }}</span>
                                    <span
                                        class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                        >{{ lesson.type }}</span
                                    >
                                    <span
                                        v-if="lesson.published_at"
                                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                        >Published</span
                                    >
                                    <span
                                        v-else
                                        class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-700"
                                        >Draft</span
                                    >
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <Link
                                        :href="
                                            route(
                                                'instructor.courses.modules.lessons.resources.create',
                                                {
                                                    section: section.id,
                                                    module: module.id,
                                                    lesson: lesson.id,
                                                }
                                            )
                                        "
                                        class="rounded text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`Upload resource for ${lesson.title}`"
                                    >
                                        + Resource
                                    </Link>
                                    <Link
                                        :href="
                                            route('instructor.courses.modules.lessons.edit', {
                                                section: section.id,
                                                module: module.id,
                                                lesson: lesson.id,
                                            })
                                        "
                                        class="inline-flex items-center gap-1 rounded text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`Edit lesson ${lesson.title}`"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Delete lesson ${lesson.title}`"
                                        @click="deleteLesson(module.id, lesson.id)"
                                    >
                                        <TrashIcon class="h-4 w-4" aria-hidden="true" />
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <ul
                                v-if="lesson.resources.length > 0"
                                class="mt-2 space-y-1 ps-4"
                                aria-label="Lesson resources"
                            >
                                <li
                                    v-for="resource in lesson.resources"
                                    :key="resource.id"
                                    class="flex items-center justify-between text-xs text-gray-600"
                                >
                                    <span>{{ resource.title }}</span>
                                    <button
                                        type="button"
                                        class="rounded text-red-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Delete resource ${resource.title}`"
                                        @click="deleteResource(module.id, lesson.id, resource.id)"
                                    >
                                        Remove
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-else class="px-5 py-3 text-sm text-gray-400">No lessons yet.</div>
                </article>
            </div>
        </div>

        <!-- Confirm dialog (native) -->
        <div
            v-if="confirm.visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-dialog-title"
        >
            <div class="mx-4 max-w-sm rounded-xl bg-white p-6 shadow-xl ring-1 ring-gray-200">
                <h2 id="confirm-dialog-title" class="font-semibold text-gray-900">
                    Confirm Delete
                </h2>
                <p class="mt-2 text-sm text-gray-600">{{ confirm.message }}</p>
                <div class="mt-5 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @click="closeConfirm"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                        @click="executeConfirm"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
