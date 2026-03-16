<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeadlineItem from '@/Components/DeadlineItem.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    upcoming: Array<{
        id: number;
        title: string;
        course_name: string;
        course_code?: string;
        due_at: string;
    }>;
    recentGrades: Array<{
        assignment_title: string;
        score: number;
        max_score: number;
        course_name: string;
    }>;
    courseSummary: Array<{
        id: number;
        code: string;
        title: string;
        section_name: string;
    }>;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- What's Next? -->
                <section aria-labelledby="whats-next-heading">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3
                                id="whats-next-heading"
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                What's Next?
                            </h3>

                            <div
                                v-if="upcoming.length === 0"
                                role="status"
                                class="text-sm text-gray-500"
                            >
                                No upcoming deadlines in the next 7 days. Great work!
                            </div>

                            <ol v-else class="space-y-3" aria-label="Upcoming deadlines">
                                <DeadlineItem
                                    v-for="assignment in upcoming"
                                    :key="assignment.id"
                                    :assignment="assignment"
                                />
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- My Courses -->
                <section v-if="courseSummary.length > 0" aria-labelledby="courses-heading">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3
                                id="courses-heading"
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                My Courses
                            </h3>

                            <ul
                                class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                                aria-label="Enrolled courses"
                            >
                                <li
                                    v-for="course in courseSummary"
                                    :key="course.id"
                                    class="rounded-lg border border-gray-200 p-4"
                                >
                                    <p class="font-medium text-sm text-gray-900">
                                        {{ course.code }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ course.title }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ course.section_name }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Recent Grades -->
                <section v-if="recentGrades.length > 0" aria-labelledby="grades-heading">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3
                                id="grades-heading"
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Recent Grades
                            </h3>

                            <ul class="divide-y divide-gray-100" aria-label="Recent grades">
                                <li
                                    v-for="(grade, i) in recentGrades"
                                    :key="i"
                                    class="flex items-center justify-between py-3 text-sm"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ grade.assignment_title }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ grade.course_name }}</p>
                                    </div>
                                    <span
                                        class="font-semibold text-gray-800"
                                        :aria-label="`${grade.score} out of ${grade.max_score}`"
                                    >
                                        {{ grade.score }}/{{ grade.max_score }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
