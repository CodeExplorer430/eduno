<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    course_section_id: number;
}

defineProps<{
    assignments: Assignment[];
}>();
</script>

<template>
    <Head title="My Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">My Assignments</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <div
                        v-if="assignments.length > 0"
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                        role="list"
                        aria-label="Assignments"
                    >
                        <div
                            v-for="(assignment, index) in assignments"
                            :key="assignment.id"
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: ${index * 80}ms`"
                            role="listitem"
                        >
                            <AssignmentCard :assignment="assignment" />
                        </div>
                    </div>

                    <EmptyState
                        v-else
                        :icon="ClipboardDocumentListIcon"
                        title="No assignments found."
                        description="Your instructors have not published any assignments yet."
                    />
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
