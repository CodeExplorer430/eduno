<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon, PencilSquareIcon, ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

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

const formatDate = (dateStr: string | null): string => {
    if (!dateStr) return 'No deadline';
    return new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateStr));
};

const isPastDue = (dateStr: string | null): boolean => {
    if (!dateStr) return false;
    return new Date(dateStr) < new Date();
};
</script>

<template>
    <Head title="Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">
                    Assignments —
                    {{ props.section.course?.title ?? 'Section' }}
                    <span class="text-base font-normal text-gray-500"
                        >({{ props.section.section_name }})</span
                    >
                </h1>
                <Link
                    :href="
                        route('instructor.courses.assignments.create', { course: props.section.id })
                    "
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    New Assignment
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <EmptyState
                v-if="props.assignments.length === 0"
                :icon="ClipboardDocumentListIcon"
                title="No assignments yet."
                description="Create one to get started."
            />

            <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900">Assignment List</h2>
                </div>
                <ul class="divide-y divide-gray-100" role="list" aria-label="Assignments">
                    <li
                        v-for="assignment in props.assignments"
                        :key="assignment.id"
                        class="flex items-center justify-between px-6 py-4 transition-colors hover:bg-gray-50"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ assignment.title }}</p>
                            <p
                                class="mt-0.5 text-sm"
                                :class="
                                    isPastDue(assignment.due_at) ? 'text-red-600' : 'text-gray-500'
                                "
                            >
                                Due: {{ formatDate(assignment.due_at) }}
                                <span
                                    v-if="isPastDue(assignment.due_at)"
                                    class="ml-1.5 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700"
                                    >Past Due</span
                                >
                            </p>
                            <p class="mt-0.5 text-xs text-gray-400">
                                Max score: {{ assignment.max_score }}
                            </p>
                        </div>
                        <Link
                            :href="
                                route('instructor.assignments.edit', { assignment: assignment.id })
                            "
                            class="inline-flex items-center gap-1 rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :aria-label="`Edit ${assignment.title}`"
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
