<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CourseCard from '@/Components/CourseCard.vue';
import { Head } from '@inertiajs/vue3';

interface Section {
    id: number;
    section_name: string;
    schedule_text: string | null;
    course: { id: number; code: string; title: string; status: string };
    instructor: { id: number; name: string };
}

defineProps<{
    sections: Section[];
}>();
</script>

<template>
    <Head title="My Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">My Courses</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <div
                        v-if="sections.length > 0"
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                        role="list"
                        aria-label="Enrolled courses"
                    >
                        <div v-for="section in sections" :key="section.id" role="listitem">
                            <CourseCard :section="section" />
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
                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
                            />
                        </svg>
                        <p class="text-base font-medium text-gray-500">
                            You are not enrolled in any courses.
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            Contact your instructor or administrator to get enrolled.
                        </p>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
