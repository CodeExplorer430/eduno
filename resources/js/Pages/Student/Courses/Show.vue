<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import { Head, Link } from '@inertiajs/vue3';

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
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('student.courses.index')"
                            class="hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                        >
                            My Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ section.course.code }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- Course info -->
                <section
                    aria-labelledby="course-info-heading"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <div class="border-b border-gray-100 bg-indigo-50 px-6 py-4">
                        <span
                            class="block text-xs font-semibold uppercase tracking-wide text-indigo-600"
                        >
                            {{ section.course.code }}
                        </span>
                        <h1 id="course-info-heading" class="mt-0.5 text-xl font-bold text-gray-900">
                            {{ section.course.title }}
                        </h1>
                    </div>
                    <div class="px-6 py-5">
                        <p v-if="section.course.description" class="mb-4 text-sm text-gray-600">
                            {{ section.course.description }}
                        </p>
                        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                            <div>
                                <dt class="font-medium text-gray-700">Section</dt>
                                <dd class="text-gray-600">{{ section.section_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-700">Instructor</dt>
                                <dd class="text-gray-600">{{ section.instructor.name }}</dd>
                            </div>
                            <div v-if="section.schedule_text">
                                <dt class="font-medium text-gray-700">Schedule</dt>
                                <dd class="text-gray-600">{{ section.schedule_text }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <!-- Modules & Lessons -->
                <section aria-labelledby="modules-heading">
                    <h2 id="modules-heading" class="mb-4 text-lg font-semibold text-gray-800">
                        Course Modules
                    </h2>

                    <div v-if="sortedModules.length > 0" class="space-y-4">
                        <article
                            v-for="module in sortedModules"
                            :key="module.id"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <header class="border-b border-gray-100 bg-gray-50 px-5 py-3">
                                <h3 class="font-medium text-gray-900">
                                    <span class="me-2 text-sm text-gray-400"
                                        >{{ module.order_no }}.</span
                                    >
                                    {{ module.title }}
                                </h3>
                            </header>

                            <div v-if="module.lessons.length > 0" class="px-5 py-3">
                                <ul class="divide-y divide-gray-100" aria-label="Lessons">
                                    <li
                                        v-for="lesson in module.lessons"
                                        :key="lesson.id"
                                        class="flex items-center justify-between py-2 text-sm"
                                    >
                                        <span class="text-gray-800">{{ lesson.title }}</span>
                                        <span
                                            class="ms-3 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                            aria-label="Lesson type"
                                        >
                                            {{ lessonTypeLabel(lesson.type) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="px-5 py-3 text-sm text-gray-400">
                                No lessons in this module yet.
                            </div>
                        </article>
                    </div>

                    <p
                        v-else
                        class="rounded-lg border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                    >
                        No modules have been published for this course yet.
                    </p>
                </section>

                <!-- Assignments -->
                <section aria-labelledby="assignments-heading">
                    <h2 id="assignments-heading" class="mb-4 text-lg font-semibold text-gray-800">
                        Assignments
                    </h2>

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
                        class="rounded-lg border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                    >
                        No assignments have been published for this course yet.
                    </p>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
