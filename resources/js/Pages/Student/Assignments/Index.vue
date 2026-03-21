<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import { Head } from '@inertiajs/vue3';

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
                        <div v-for="assignment in assignments" :key="assignment.id" role="listitem">
                            <AssignmentCard :assignment="assignment" />
                        </div>
                    </div>

                    <section
                        v-else
                        aria-label="Empty state"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white py-20 text-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mb-4 h-12 w-12 text-gray-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"
                            />
                        </svg>
                        <p class="text-base font-medium text-gray-500">No assignments found.</p>
                        <p class="mt-1 text-sm text-gray-400">
                            Your instructors have not published any assignments yet.
                        </p>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
