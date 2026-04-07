<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, PencilSquareIcon, BookOpenIcon } from '@heroicons/vue/24/outline';

interface LessonItem {
    id: number;
    title: string;
    type: string;
    order_no: number;
    published_at: string | null;
}

interface ModuleInfo {
    id: number;
    title: string;
}

interface SectionInfo {
    id: number;
    section_name: string;
    course?: { id: number; code: string; title: string };
}

const props = defineProps<{
    section: SectionInfo;
    module: ModuleInfo;
    lessons: LessonItem[];
}>();

const typeBadgeClass: Record<string, string> = {
    text: 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300',
    pdf: 'bg-red-100 text-red-700',
    video: 'bg-purple-100 text-purple-700',
    link: 'bg-blue-100 text-blue-700',
};
</script>

<template>
    <Head :title="`Lessons — ${props.module.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'Courses', href: route('instructor.courses.index') },
                    {
                        label: `${props.section.course?.code ?? ''} — ${props.section.section_name}`,
                        href: route('instructor.courses.modules.index', props.section.id),
                    },
                    { label: props.module.title },
                    { label: 'Lessons' },
                ]"
            />
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <header class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ props.module.title }}
                        <span class="text-base font-normal text-gray-500 dark:text-slate-400"
                            >— Lessons</span
                        >
                    </h1>
                    <p
                        v-if="props.section.course"
                        class="mt-1 text-sm text-gray-500 dark:text-slate-400"
                    >
                        <span class="font-mono font-medium">{{ props.section.course.code }}</span>
                        — {{ props.section.course.title }} · Section
                        {{ props.section.section_name }}
                    </p>
                </div>

                <Link
                    :href="
                        route('instructor.courses.modules.lessons.create', {
                            section: props.section.id,
                            module: props.module.id,
                        })
                    "
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    New Lesson
                </Link>
            </header>

            <EmptyState
                v-if="props.lessons.length === 0"
                :icon="BookOpenIcon"
                title="No lessons yet."
                description="Add the first lesson to this module."
            />

            <div
                v-else
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Lesson List</h2>
                    <span class="ml-auto text-xs text-gray-400 dark:text-slate-500">
                        {{ props.lessons.length }}
                        {{ props.lessons.length === 1 ? 'lesson' : 'lessons' }}
                    </span>
                </div>

                <ul
                    class="divide-y divide-gray-100 dark:divide-slate-700"
                    role="list"
                    aria-label="Lessons"
                >
                    <li
                        v-for="lesson in props.lessons"
                        :key="lesson.id"
                        class="flex items-center justify-between px-6 py-4 transition-colors hover:bg-gray-50 dark:hover:bg-slate-700/50"
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <span
                                class="w-6 shrink-0 text-center text-xs font-medium text-gray-400 dark:text-slate-500"
                                aria-hidden="true"
                            >
                                {{ lesson.order_no }}
                            </span>
                            <span
                                class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                :class="
                                    typeBadgeClass[lesson.type] ??
                                    'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-gray-300'
                                "
                            >
                                {{ lesson.type }}
                            </span>
                            <p class="truncate font-medium text-gray-900 dark:text-white">
                                {{ lesson.title }}
                            </p>
                            <span
                                v-if="lesson.published_at"
                                class="shrink-0 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700"
                            >
                                Published
                            </span>
                            <span
                                v-else
                                class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                            >
                                Draft
                            </span>
                        </div>

                        <Link
                            :href="
                                route('instructor.courses.modules.lessons.edit', {
                                    section: props.section.id,
                                    module: props.module.id,
                                    lesson: lesson.id,
                                })
                            "
                            class="ml-4 inline-flex shrink-0 items-center gap-1 rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :aria-label="`Edit ${lesson.title}`"
                        >
                            <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                            Edit
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
