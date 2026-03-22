<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

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
                        class="inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <PlusIcon class="h-4 w-4" aria-hidden="true" />
                        New Assignment
                    </Link>
                </div>

                <EmptyState
                    v-if="props.assignments.length === 0"
                    title="No assignments yet."
                    description="Create one to get started."
                />

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
                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-label="`Edit ${assignment.title}`"
                            >
                                <PencilSquareIcon class="me-1 inline h-4 w-4" aria-hidden="true" />
                                Edit
                            </Link>
                        </div>
                    </li>
                </ul>
            </div>
        </main>
    </AuthenticatedLayout>
</template>
