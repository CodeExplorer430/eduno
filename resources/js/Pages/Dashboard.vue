<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import {
    BookOpenIcon,
    ClipboardDocumentListIcon,
    ChartBarIcon,
    UsersIcon,
    MegaphoneIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline';
import type { Assignment, Announcement, Grade, Submission } from '@/Types/models';
import { useFormatDate } from '@/composables/useFormatDate';

interface StudentProps {
    role: 'student';
    enrolled_courses_count: number;
    upcoming_assignments: Assignment[];
    recent_announcements: (Announcement & {
        course_section: {
            id: number;
            section_name: string;
            course: { code: string; title: string };
        };
        author: { id: number; name: string };
    })[];
    latest_grade: (Grade & { submission: { assignment: Assignment } }) | null;
}

interface InstructorProps {
    role: 'instructor';
    courses_count: number;
    pending_submissions_count: number;
    recent_submissions: (Submission & {
        assignment: Assignment;
        student: { id: number; name: string };
    })[];
    upcoming_deadlines: Assignment[];
}

interface AdminProps {
    role: 'admin';
    users_by_role: { student: number; instructor: number; admin: number };
    total_courses: number;
    total_submissions: number;
    total_grades_released: number;
}

type DashboardProps = StudentProps | InstructorProps | AdminProps;

const props = defineProps<DashboardProps>();

const { formatDate } = useFormatDate();

const page = usePage<PageProps>();
const userName = computed(() => page.props.auth?.user?.name ?? '');
const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Good morning';
    if (h < 17) return 'Good afternoon';
    return 'Good evening';
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-12">
            <!-- Greeting banner -->
            <div class="mx-auto mb-8 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-6 text-white shadow-sm"
                >
                    <p class="text-xl font-semibold">{{ greeting }}, {{ userName }}!</p>
                    <p class="mt-1 text-sm text-white/80">Here's your Eduno overview.</p>
                </div>
            </div>

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Student Dashboard -->
                <div v-if="props.role === 'student'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="student-stats-heading">
                        <h2 id="student-stats-heading" class="sr-only">Your statistics</h2>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <StatCard
                                label="Enrolled Courses"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ (props as StudentProps).enrolled_courses_count }}
                            </StatCard>
                            <StatCard
                                label="Upcoming Assignments"
                                :icon="ClipboardDocumentListIcon"
                                :animation-delay="80"
                                accent="amber"
                            >
                                {{ (props as StudentProps).upcoming_assignments.length }}
                            </StatCard>
                            <StatCard
                                label="Latest Grade"
                                :icon="ChartBarIcon"
                                :animation-delay="160"
                                accent="green"
                            >
                                <template v-if="(props as StudentProps).latest_grade">
                                    {{ (props as StudentProps).latest_grade!.score }}
                                </template>
                                <span v-else class="text-lg text-gray-400">None yet</span>
                            </StatCard>
                        </dl>
                    </section>

                    <!-- Upcoming Assignments -->
                    <section
                        aria-labelledby="upcoming-assignments-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-gray-100 border-l-4 border-l-blue-500 px-6 py-4"
                        >
                            <h2
                                id="upcoming-assignments-heading"
                                class="font-semibold text-gray-800"
                            >
                                Upcoming Assignments (next 7 days)
                            </h2>
                        </div>
                        <div
                            v-if="(props as StudentProps).upcoming_assignments.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="assignment in (props as StudentProps).upcoming_assignments"
                                :key="assignment.id"
                                class="flex items-center justify-between px-6 py-3 text-sm"
                            >
                                <span class="flex items-start gap-3 font-medium text-gray-800">
                                    <ClockIcon
                                        class="h-4 w-4 shrink-0 text-gray-400"
                                        aria-hidden="true"
                                    />
                                    {{ assignment.title }}
                                </span>
                                <time :datetime="assignment.due_at ?? ''" class="text-gray-500">
                                    {{ formatDate(assignment.due_at) }}
                                </time>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-gray-400">
                            No assignments due in the next 7 days.
                        </p>
                    </section>

                    <!-- Recent Announcements -->
                    <section
                        aria-labelledby="recent-announcements-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div
                            class="flex items-center justify-between border-b border-l-4 border-gray-100 border-l-blue-500 px-6 py-4"
                        >
                            <h2
                                id="recent-announcements-heading"
                                class="font-semibold text-gray-800"
                            >
                                Recent Announcements
                            </h2>
                            <Link
                                :href="route('student.announcements.index')"
                                class="rounded text-sm text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                View all
                            </Link>
                        </div>
                        <div
                            v-if="(props as StudentProps).recent_announcements.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="announcement in (props as StudentProps).recent_announcements"
                                :key="announcement.id"
                                class="flex items-start gap-3 px-6 py-3"
                            >
                                <MegaphoneIcon
                                    class="mt-0.5 h-4 w-4 shrink-0 text-blue-400"
                                    aria-hidden="true"
                                />
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ announcement.title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        {{ announcement.course_section.course.code }} &bull;
                                        {{ announcement.author.name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-gray-400">
                            No recent announcements.
                        </p>
                    </section>
                </div>

                <!-- Instructor Dashboard -->
                <div v-else-if="props.role === 'instructor'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="instructor-stats-heading">
                        <h2 id="instructor-stats-heading" class="sr-only">Your statistics</h2>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <StatCard
                                label="Course Sections"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ (props as InstructorProps).courses_count }}
                            </StatCard>
                            <StatCard
                                label="Pending Submissions"
                                :icon="ClipboardDocumentListIcon"
                                value-class="text-blue-600"
                                :animation-delay="80"
                                accent="cyan"
                            >
                                {{ (props as InstructorProps).pending_submissions_count }}
                            </StatCard>
                        </dl>
                    </section>

                    <!-- Upcoming Deadlines -->
                    <section
                        aria-labelledby="instructor-deadlines-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-gray-100 border-l-4 border-l-blue-500 px-6 py-4"
                        >
                            <h2
                                id="instructor-deadlines-heading"
                                class="font-semibold text-gray-800"
                            >
                                Upcoming Deadlines (next 7 days)
                            </h2>
                        </div>
                        <div
                            v-if="(props as InstructorProps).upcoming_deadlines.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="assignment in (props as InstructorProps).upcoming_deadlines"
                                :key="assignment.id"
                                class="flex items-center justify-between px-6 py-3 text-sm"
                            >
                                <span class="flex items-start gap-3 font-medium text-gray-800">
                                    <ClockIcon
                                        class="h-4 w-4 shrink-0 text-gray-400"
                                        aria-hidden="true"
                                    />
                                    {{ assignment.title }}
                                </span>
                                <time :datetime="assignment.due_at ?? ''" class="text-gray-500">
                                    {{ formatDate(assignment.due_at) }}
                                </time>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-gray-400">
                            No deadlines in the next 7 days.
                        </p>
                    </section>

                    <!-- Recent Submissions -->
                    <section
                        aria-labelledby="recent-submissions-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-gray-100 border-l-4 border-l-blue-500 px-6 py-4"
                        >
                            <h2 id="recent-submissions-heading" class="font-semibold text-gray-800">
                                Recent Submissions
                            </h2>
                        </div>
                        <div
                            v-if="(props as InstructorProps).recent_submissions.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="submission in (props as InstructorProps).recent_submissions"
                                :key="submission.id"
                                class="flex items-center justify-between px-6 py-3 text-sm"
                            >
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ submission.student.name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ submission.assignment.title }}
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700"
                                >
                                    {{ submission.status }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-gray-400">No recent submissions.</p>
                    </section>
                </div>

                <!-- Admin Dashboard -->
                <div v-else-if="props.role === 'admin'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="admin-stats-heading">
                        <h2 id="admin-stats-heading" class="sr-only">System statistics</h2>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <StatCard
                                label="Total Courses"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ (props as AdminProps).total_courses }}
                            </StatCard>
                            <StatCard
                                label="Total Submissions"
                                :icon="ClipboardDocumentListIcon"
                                :animation-delay="80"
                                accent="cyan"
                            >
                                {{ (props as AdminProps).total_submissions }}
                            </StatCard>
                            <StatCard
                                label="Grades Released"
                                :icon="ChartBarIcon"
                                value-class="text-green-600"
                                :animation-delay="160"
                                accent="green"
                            >
                                {{ (props as AdminProps).total_grades_released }}
                            </StatCard>
                        </dl>
                    </section>

                    <!-- Users by Role -->
                    <section
                        aria-labelledby="users-by-role-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-gray-100 border-l-4 border-l-blue-500 px-6 py-4"
                        >
                            <h2 id="users-by-role-heading" class="font-semibold text-gray-800">
                                Users by Role
                            </h2>
                        </div>
                        <dl class="grid grid-cols-3 divide-x divide-gray-100">
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 0ms`"
                                class="px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-gray-500">Students</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ (props as AdminProps).users_by_role.student }}
                                </dd>
                            </div>
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 80ms`"
                                class="px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-gray-500">Instructors</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ (props as AdminProps).users_by_role.instructor }}
                                </dd>
                            </div>
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 160ms`"
                                class="px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-gray-400"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-gray-500">Admins</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ (props as AdminProps).users_by_role.admin }}
                                </dd>
                            </div>
                        </dl>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
