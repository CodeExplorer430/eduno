<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CourseCard from '@/Components/CourseCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { BookOpenIcon } from '@heroicons/vue/24/outline';

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
                        <div
                            v-for="(section, index) in sections"
                            :key="section.id"
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: ${index * 80}ms`"
                            role="listitem"
                        >
                            <CourseCard :section="section" />
                        </div>
                    </div>

                    <EmptyState
                        v-else
                        :icon="BookOpenIcon"
                        title="You are not enrolled in any courses."
                        description="Contact your instructor or administrator to get enrolled."
                    />
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
