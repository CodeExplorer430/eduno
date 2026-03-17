<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface AssignmentItem {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    allow_resubmission: boolean;
    published_at: string | null;
}

interface Section {
    id: number;
    section_name: string;
    course?: { id: number; code: string; title: string };
}

const props = defineProps<{
    section: Section;
    assignments: AssignmentItem[];
}>();
</script>

<template>
    <Head title="Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Assignments —
                {{ props.section.course?.title ?? 'Section' }}
                ({{ props.section.section_name }})
            </h2>
        </template>

        <main class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Assignment List</h3>
                    <Link
                        :href="
                            route('instructor.courses.assignments.create', {
                                course: props.section.id,
                            })
                        "
                        class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        New Assignment
                    </Link>
                </div>

                <div
                    v-if="props.assignments.length === 0"
                    class="rounded border border-dashed border-gray-300 py-12 text-center text-gray-500"
                    role="status"
                    aria-live="polite"
                >
                    No assignments yet. Create one to get started.
                </div>

                <ul
                    v-else
                    class="divide-y divide-gray-200 rounded border border-gray-200 bg-white"
                    role="list"
                >
                    <li
                        v-for="assignment in props.assignments"
                        :key="assignment.id"
                        class="flex items-center justify-between px-4 py-3"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ assignment.title }}</p>
                            <p class="text-sm text-gray-500">
                                Due: {{ assignment.due_at ?? 'No deadline' }} · Max score:
                                {{ assignment.max_score }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="
                                    route('instructor.assignments.edit', {
                                        assignment: assignment.id,
                                    })
                                "
                                class="text-sm text-indigo-600 hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :aria-label="`Edit ${assignment.title}`"
                            >
                                Edit
                            </Link>
                        </div>
                    </li>
                </ul>
            </div>
        </main>
    </AuthenticatedLayout>
</template>
