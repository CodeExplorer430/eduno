<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpenIcon } from '@heroicons/vue/24/outline';

interface LessonSummary {
    id: number;
    title: string;
    type: string;
    order_no: number;
    module: {
        id: number;
        title: string;
        section: {
            id: number;
            section_name: string;
            course: { code: string; title: string };
        };
    };
}

const props = defineProps<{
    lessons: LessonSummary[];
}>();

interface LessonGroup {
    moduleId: number;
    moduleTitle: string;
    courseCode: string;
    courseTitle: string;
    lessons: LessonSummary[];
}

const groups = computed<LessonGroup[]>(() => {
    const map = new Map<number, LessonGroup>();
    for (const lesson of props.lessons) {
        const mid = lesson.module.id;
        if (!map.has(mid)) {
            map.set(mid, {
                moduleId: mid,
                moduleTitle: lesson.module.title,
                courseCode: lesson.module.section.course.code,
                courseTitle: lesson.module.section.course.title,
                lessons: [],
            });
        }
        map.get(mid)!.lessons.push(lesson);
    }
    return Array.from(map.values());
});

const typeBadgeClass: Record<string, string> = {
    text: 'bg-gray-100 text-gray-700 dark:text-gray-300',
    pdf: 'bg-red-100 text-red-700',
    video: 'bg-purple-100 text-purple-700',
    link: 'bg-blue-100 text-blue-700',
};
</script>

<template>
    <Head title="My Lessons" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                My Lessons
            </h1>
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <EmptyState
                v-if="props.lessons.length === 0"
                :icon="BookOpenIcon"
                title="No lessons yet."
                description="Your instructors have not published any lessons yet."
            />

            <div v-else class="space-y-8">
                <section
                    v-for="group in groups"
                    :key="group.moduleId"
                    :aria-label="`${group.courseCode} — ${group.moduleTitle}`"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 border-l-4 border-blue-200 pl-3 text-sm font-semibold uppercase tracking-wider text-blue-600"
                    >
                        <span
                            class="rounded bg-blue-50 px-1.5 py-0.5 font-mono text-xs font-medium text-blue-700"
                        >
                            {{ group.courseCode }}
                        </span>
                        {{ group.moduleTitle }}
                        <span
                            class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-normal normal-case text-gray-600 dark:text-gray-400 dark:text-slate-500"
                        >
                            {{ group.lessons.length }}
                        </span>
                    </h2>

                    <div
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 dark:bg-slate-800 ring-gray-100 dark:ring-slate-700"
                    >
                        <ul class="divide-y divide-gray-100 dark:divide-slate-700" role="list">
                            <li
                                v-for="lesson in group.lessons"
                                :key="lesson.id"
                                class="flex items-center justify-between px-6 py-4 transition-colors hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-700/50"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            typeBadgeClass[lesson.type] ??
                                            'bg-gray-100 text-gray-700 dark:text-gray-300'
                                        "
                                    >
                                        {{ lesson.type }}
                                    </span>
                                    <span
                                        class="truncate text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ lesson.title }}
                                    </span>
                                </div>

                                <Link
                                    :href="route('student.lessons.show', lesson.id)"
                                    class="ml-4 shrink-0 text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                                    :aria-label="`View lesson: ${lesson.title}`"
                                >
                                    View →
                                </Link>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
