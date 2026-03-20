<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeadlineItem from '@/Components/DeadlineItem.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    // Student props
    upcoming?: Array<{
        id: number;
        title: string;
        course_name: string;
        course_code?: string;
        due_at: string;
    }>;
    recentGrades?: Array<{
        assignment_title: string;
        score: number;
        max_score: number;
        course_name: string;
    }>;
    courseSummary?: Array<{
        id: number;
        code: string;
        title: string;
        section_name: string;
    }>;
    // Instructor props
    sections?: Array<{
        id: number;
        course: { code: string; title: string };
        section_name: string;
        enrollments_count: number;
        assignments_count: number;
    }>;
    pendingSubmissions?: Array<{
        id: number;
        student: { name: string };
        assignment: { title: string; section: { course: { code: string } } };
        submitted_at: string;
        is_late: boolean;
    }>;
}>();

const showWelcome = ref(!localStorage.getItem('eduno_welcome_dismissed'));

function dismissWelcome(): void {
    localStorage.setItem('eduno_welcome_dismissed', '1');
    showWelcome.value = false;
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- Welcome banner -->
                <div
                    v-if="showWelcome"
                    role="status"
                    aria-live="polite"
                    class="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800"
                >
                    <span>
                        Welcome to Eduno! Start by visiting your Courses to access materials and
                        assignments.
                    </span>
                    <button
                        type="button"
                        class="ml-4 shrink-0 rounded p-1 hover:bg-blue-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                        aria-label="Dismiss welcome banner"
                        @click="dismissWelcome"
                    >
                        ✕
                    </button>
                </div>

                <!-- Student-only view: upcoming assignments, enrolled courses, recent grades -->
                <template v-if="upcoming !== undefined">
                    <!-- What's Next? -->
                    <section aria-labelledby="whats-next-heading">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3
                                    id="whats-next-heading"
                                    class="mb-4 text-lg font-semibold text-gray-900"
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
                    <section
                        v-if="courseSummary !== undefined && courseSummary.length > 0"
                        aria-labelledby="courses-heading"
                    >
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3
                                    id="courses-heading"
                                    class="mb-4 text-lg font-semibold text-gray-900"
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
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ course.code }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ course.title }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ course.section_name }}
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Recent Grades -->
                    <section
                        v-if="recentGrades !== undefined && recentGrades.length > 0"
                        aria-labelledby="grades-heading"
                    >
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3
                                    id="grades-heading"
                                    class="mb-4 text-lg font-semibold text-gray-900"
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
                                            <p class="text-xs text-gray-500">
                                                {{ grade.course_name }}
                                            </p>
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
                </template>

                <!-- Instructor-only view: sections taught, pending submissions to grade -->
                <template v-if="sections !== undefined">
                    <!-- My Sections -->
                    <section aria-labelledby="my-sections-heading">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3
                                    id="my-sections-heading"
                                    class="mb-4 text-lg font-semibold text-gray-900"
                                >
                                    My Sections
                                </h3>

                                <div
                                    v-if="sections.length === 0"
                                    class="text-sm text-gray-500"
                                    role="status"
                                >
                                    You have no sections assigned yet.
                                </div>

                                <ul
                                    v-else
                                    class="divide-y divide-gray-100"
                                    aria-label="My sections"
                                >
                                    <li
                                        v-for="section in sections"
                                        :key="section.id"
                                        class="flex items-center justify-between py-3 text-sm"
                                    >
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ section.course.code }} —
                                                {{ section.section_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ section.course.title }}
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-500">
                                            {{ section.enrollments_count }} enrolled ·
                                            {{ section.assignments_count }} assignments
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Pending Grading -->
                    <section
                        v-if="pendingSubmissions !== undefined"
                        aria-labelledby="pending-grading-heading"
                    >
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3
                                    id="pending-grading-heading"
                                    class="mb-4 text-lg font-semibold text-gray-900"
                                >
                                    Pending Grading
                                </h3>

                                <div
                                    v-if="pendingSubmissions.length === 0"
                                    class="text-sm text-gray-500"
                                    role="status"
                                >
                                    No ungraded submissions. All caught up!
                                </div>

                                <ul
                                    v-else
                                    class="divide-y divide-gray-100"
                                    aria-label="Pending submissions to grade"
                                >
                                    <li
                                        v-for="sub in pendingSubmissions"
                                        :key="sub.id"
                                        class="flex items-center justify-between py-3 text-sm"
                                    >
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ sub.student.name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ sub.assignment.section.course.code }} —
                                                {{ sub.assignment.title }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ new Date(sub.submitted_at).toLocaleString() }}
                                                <span
                                                    v-if="sub.is_late"
                                                    class="ml-1 rounded-full bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-700"
                                                >
                                                    Late
                                                </span>
                                            </p>
                                        </div>
                                        <Link
                                            :href="route('submissions.show', sub.id)"
                                            class="ml-4 shrink-0 text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600"
                                        >
                                            Grade
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
