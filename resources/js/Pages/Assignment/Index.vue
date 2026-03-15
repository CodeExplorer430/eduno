<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Assignment, CourseSection } from '@/Types/models';

defineProps<{
    section: CourseSection;
    assignments: Assignment[];
    canManage: boolean;
}>();

const publishForm = useForm({});

function togglePublish(assignment: Assignment): void {
    publishForm.post(route('assignments.publish', assignment.id));
}

function statusLabel(assignment: Assignment): string {
    if (!assignment.published_at) return 'Draft';
    if (assignment.due_at && new Date(assignment.due_at) < new Date()) return 'Past Due';
    return 'Published';
}

function statusClass(assignment: Assignment): string {
    if (!assignment.published_at) return 'bg-gray-100 text-gray-600';
    if (assignment.due_at && new Date(assignment.due_at) < new Date())
        return 'bg-red-100 text-red-700';
    return 'bg-green-100 text-green-700';
}
</script>

<template>
    <Head :title="`Assignments — ${section.section_name}`" />

    <main class="mx-auto max-w-5xl px-4 py-8">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <nav aria-label="Breadcrumb">
                    <ol class="flex gap-2 text-sm text-gray-500">
                        <li>
                            <Link
                                :href="route('sections.show', section.id)"
                                class="hover:underline"
                            >
                                {{ section.section_name }}
                            </Link>
                        </li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page">Assignments</li>
                    </ol>
                </nav>
                <h1 class="mt-1 text-2xl font-bold">Assignments</h1>
            </div>

            <Link
                v-if="canManage"
                :href="route('sections.assignments.create', section.id)"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                New Assignment
            </Link>
        </header>

        <div
            v-if="assignments.length === 0"
            class="rounded border border-dashed border-gray-300 px-6 py-12 text-center text-gray-500"
            role="status"
        >
            No assignments yet.
        </div>

        <table v-else class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-left text-gray-600">
                    <th scope="col" class="py-3 pr-4 font-medium">Title</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Due Date</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Status</th>
                    <th scope="col" class="py-3 pr-4 font-medium">Max Score</th>
                    <th v-if="canManage" scope="col" class="py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="assignment in assignments"
                    :key="assignment.id"
                    class="border-b border-gray-100 hover:bg-gray-50"
                >
                    <td class="py-3 pr-4">
                        <Link
                            :href="route('assignments.show', assignment.id)"
                            class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        >
                            {{ assignment.title }}
                        </Link>
                    </td>
                    <td class="py-3 pr-4 text-gray-600">
                        {{ assignment.due_at ? new Date(assignment.due_at).toLocaleString() : '—' }}
                    </td>
                    <td class="py-3 pr-4">
                        <span
                            :class="statusClass(assignment)"
                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                        >
                            {{ statusLabel(assignment) }}
                        </span>
                    </td>
                    <td class="py-3 pr-4 text-gray-600">{{ assignment.max_score }}</td>
                    <td v-if="canManage" class="py-3">
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="text-blue-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                                :aria-label="`${assignment.published_at ? 'Unpublish' : 'Publish'} ${assignment.title}`"
                                @click="togglePublish(assignment)"
                            >
                                {{ assignment.published_at ? 'Unpublish' : 'Publish' }}
                            </button>
                            <Link
                                :href="route('assignments.edit', assignment.id)"
                                class="text-gray-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                                :aria-label="`Edit ${assignment.title}`"
                            >
                                Edit
                            </Link>
                            <Link
                                :href="route('assignments.destroy', assignment.id)"
                                method="delete"
                                as="button"
                                class="text-red-600 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-red-600"
                                :aria-label="`Delete ${assignment.title}`"
                            >
                                Delete
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</template>
