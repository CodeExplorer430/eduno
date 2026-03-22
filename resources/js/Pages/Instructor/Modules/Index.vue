<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
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

const confirmDialog = ref<{
    visible: boolean;
    message: string;
    onConfirm: () => void;
}>({ visible: false, message: '', onConfirm: () => {} });

const openConfirm = (message: string, action: () => void): void => {
    confirmDialog.value = { visible: true, message, onConfirm: action };
};
const closeConfirm = (): void => {
    confirmDialog.value = { visible: false, message: '', onConfirm: () => {} };
};

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
                <div>
                    <Breadcrumb
                        :crumbs="[
                            { label: 'My Courses', href: route('instructor.courses.index') },
                            { label: `${section.course.code} — Modules` },
                        ]"
                    />
                </div>
                <Link
                    :href="route('instructor.courses.modules.create', section.id)"
                    class="inline-flex items-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    Add Module
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <main>
                    <div v-if="section.modules.length > 0" class="space-y-6">
                        <article
                            v-for="module in section.modules"
                            :key="module.id"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                            :aria-labelledby="`module-title-${module.id}`"
                        >
                            <header
                                class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-3"
                            >
                                <div>
                                    <h2
                                        :id="`module-title-${module.id}`"
                                        class="font-semibold text-gray-900"
                                    >
                                        <span class="me-2 text-sm text-gray-400"
                                            >{{ module.order_no }}.</span
                                        >
                                        {{ module.title }}
                                    </h2>
                                    <span
                                        v-if="module.published_at"
                                        class="mt-1 inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                    >
                                        Published
                                    </span>
                                    <span
                                        v-else
                                        class="mt-1 inline-block rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-700"
                                    >
                                        Draft
                                    </span>
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
                                        <PencilSquareIcon
                                            class="me-1 inline h-4 w-4"
                                            aria-hidden="true"
                                        />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Delete module ${module.title}`"
                                        @click="deleteModule(module.id)"
                                    >
                                        <TrashIcon class="me-1 inline h-4 w-4" aria-hidden="true" />
                                        Delete
                                    </button>
                                </div>
                            </header>

                            <div v-if="module.lessons.length > 0" class="divide-y divide-gray-100">
                                <div
                                    v-for="lesson in module.lessons"
                                    :key="lesson.id"
                                    class="px-5 py-3"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-medium text-gray-800">
                                                {{ lesson.title }}
                                            </span>
                                            <span
                                                class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                            >
                                                {{ lesson.type }}
                                            </span>
                                            <span
                                                v-if="lesson.published_at"
                                                class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                            >
                                                Published
                                            </span>
                                            <span
                                                v-else
                                                class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs text-yellow-700"
                                            >
                                                Draft
                                            </span>
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
                                                    route(
                                                        'instructor.courses.modules.lessons.edit',
                                                        {
                                                            section: section.id,
                                                            module: module.id,
                                                            lesson: lesson.id,
                                                        }
                                                    )
                                                "
                                                class="inline-flex items-center gap-1 rounded text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :aria-label="`Edit lesson ${lesson.title}`"
                                            >
                                                <PencilSquareIcon
                                                    class="me-1 inline h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                :aria-label="`Delete lesson ${lesson.title}`"
                                                @click="deleteLesson(module.id, lesson.id)"
                                            >
                                                <TrashIcon
                                                    class="me-1 inline h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Resources -->
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
                                                @click="
                                                    deleteResource(
                                                        module.id,
                                                        lesson.id,
                                                        resource.id
                                                    )
                                                "
                                            >
                                                Remove
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-else class="px-5 py-3 text-sm text-gray-400">
                                No lessons yet.
                            </div>
                        </article>
                    </div>

                    <EmptyState
                        v-else
                        :icon="BookOpenIcon"
                        title="No modules yet."
                        description="Create your first module to get started."
                    >
                        <Link
                            :href="route('instructor.courses.modules.create', section.id)"
                            class="mt-4 inline-flex items-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <PlusIcon class="h-4 w-4" aria-hidden="true" />
                            Add Module
                        </Link>
                    </EmptyState>
                </main>
            </div>
        </div>

        <Dialog
            :visible="confirmDialog.visible"
            modal
            header="Confirm Delete"
            :closable="false"
            @update:visible="closeConfirm"
        >
            <p>{{ confirmDialog.message }}</p>
            <template #footer>
                <Button severity="secondary" @click="closeConfirm">Cancel</Button>
                <Button
                    severity="danger"
                    @click="
                        () => {
                            confirmDialog.onConfirm();
                            closeConfirm();
                        }
                    "
                >
                    Delete
                </Button>
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
