<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';

interface Lesson {
    id: number;
    title: string;
    type: string;
}

interface CourseModule {
    id: number;
    title: string;
    order_no: number;
    lessons: Lesson[];
}

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    course_section_id: number;
}

interface Section {
    id: number;
    section_name: string;
    schedule_text: string | null;
    course: { id: number; code: string; title: string; description: string | null };
    instructor: { id: number; name: string };
    modules: CourseModule[];
    assignments: Assignment[];
}

const props = defineProps<{
    section: Section;
}>();

const sortedModules = computed<CourseModule[]>(() =>
    [...props.section.modules].sort((a, b) => a.order_no - b.order_no)
);

const expandedModules = ref<Set<number>>(new Set(sortedModules.value.slice(0, 1).map((m) => m.id)));

function toggleModule(id: number): void {
    if (expandedModules.value.has(id)) {
        expandedModules.value.delete(id);
    } else {
        expandedModules.value.add(id);
    }
}

const lessonTypeLabel = (type: string): string => {
    const labels: Record<string, string> = {
        video: 'Video',
        document: 'Document',
        quiz: 'Quiz',
        text: 'Text',
    };
    return labels[type] ?? type;
};
</script>

<template>
    <Head :title="`${section.course.code} — ${section.course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'My Courses', href: route('student.courses.index') },
                    { label: section.course.code },
                ]"
            />
        </template>

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Course hero -->
            <section
                aria-labelledby="course-info-heading"
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
            >
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-6">
                    <span
                        class="block text-xs font-semibold uppercase tracking-wider text-blue-100"
                    >
                        {{ section.course.code }}
                    </span>
                    <h1 id="course-info-heading" class="mt-1 text-2xl font-bold text-white">
                        {{ section.course.title }}
                    </h1>
                </div>
                <div class="px-6 py-5">
                    <p v-if="section.course.description" class="mb-4 text-sm text-gray-600">
                        {{ section.course.description }}
                    </p>
                    <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="font-medium text-gray-500">Section</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.section_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Instructor</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.instructor.name }}</dd>
                        </div>
                        <div v-if="section.schedule_text">
                            <dt class="font-medium text-gray-500">Schedule</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.schedule_text }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- Modules -->
            <section aria-labelledby="modules-heading">
                <div class="mb-4 flex items-center gap-3">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 id="modules-heading" class="text-lg font-semibold text-gray-800">
                        Course Modules
                    </h2>
                </div>

                <div v-if="sortedModules.length > 0" class="space-y-3">
                    <article
                        v-for="module in sortedModules"
                        :key="module.id"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between px-5 py-3 text-left hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                            :aria-expanded="expandedModules.has(module.id)"
                            :aria-controls="`module-content-${module.id}`"
                            @click="toggleModule(module.id)"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                >
                                    {{ module.order_no }}
                                </span>
                                <h3 class="font-medium text-gray-900">{{ module.title }}</h3>
                            </div>
                            <ChevronDownIcon
                                class="h-5 w-5 text-gray-400 transition-transform"
                                :class="expandedModules.has(module.id) ? 'rotate-180' : ''"
                                aria-hidden="true"
                            />
                        </button>

                        <div
                            v-show="expandedModules.has(module.id)"
                            :id="`module-content-${module.id}`"
                        >
                            <div
                                v-if="module.lessons.length > 0"
                                class="px-5 py-3 border-t border-gray-100"
                            >
                                <ul class="divide-y divide-gray-100" aria-label="Lessons">
                                    <li
                                        v-for="lesson in module.lessons"
                                        :key="lesson.id"
                                        class="flex items-center justify-between py-2 text-sm"
                                    >
                                        <Link
                                            :href="route('student.lessons.show', lesson.id)"
                                            class="rounded font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            {{ lesson.title }}
                                        </Link>
                                        <span
                                            class="ms-3 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                            aria-label="Lesson type"
                                        >
                                            {{ lessonTypeLabel(lesson.type) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div
                                v-else
                                class="border-t border-gray-100 px-5 py-3 text-sm text-gray-400"
                            >
                                No lessons in this module yet.
                            </div>
                        </div>
                    </article>
                </div>

                <p
                    v-else
                    class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                >
                    No modules have been published for this course yet.
                </p>
            </section>

            <!-- Assignments -->
            <section aria-labelledby="assignments-heading">
                <div class="mb-4 flex items-center gap-3">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 id="assignments-heading" class="text-lg font-semibold text-gray-800">
                        Assignments
                    </h2>
                </div>

                <div
                    v-if="section.assignments.length > 0"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <AssignmentCard
                        v-for="assignment in section.assignments"
                        :key="assignment.id"
                        :assignment="assignment"
                    />
                </div>

                <p
                    v-else
                    class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                >
                    No assignments have been published for this course yet.
                </p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
