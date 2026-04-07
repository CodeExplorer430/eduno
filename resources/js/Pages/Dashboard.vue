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
    ExclamationTriangleIcon,
    FlagIcon,
    AcademicCapIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';
import type { Announcement, Assignment, Submission } from '@/Types/models';
import { useFormatDate } from '@/composables/useFormatDate';

interface UpcomingAssignmentSummary {
    id: number;
    title: string;
    course_name: string;
    course_code: string;
    due_at: string | null;
}

interface RecentGradeSummary {
    assignment_title: string;
    score: number;
    max_score: number;
    course_name: string;
}

interface SectionSummary {
    id: number;
    section_name: string;
    course: { id: number; code: string; title: string };
    enrollments_count: number;
    assignments_count: number;
}

interface LatestGrade {
    score: number;
    max_score: number;
    assignment: string;
    course: string;
}

interface CourseSummary {
    id: number;
    code: string;
    title: string;
    section_name: string;
}

interface RecentAnnouncement extends Omit<Announcement, 'author' | 'course_section'> {
    course_section: {
        id: number;
        section_name: string;
        course: { code: string; title: string };
    };
    author: { id: number; name: string };
}

interface RecentSubmission extends Submission {
    assignment: Assignment;
    student: { id: number; name: string };
}

interface Props {
    role: 'student' | 'instructor' | 'admin';
    // student
    enrolled_courses_count?: number;
    upcoming?: UpcomingAssignmentSummary[];
    recent_announcements?: RecentAnnouncement[];
    recent_grades?: RecentGradeSummary[];
    latest_grade?: LatestGrade | null;
    course_summary?: CourseSummary[];
    // instructor
    courses_count?: number;
    pending_submissions_count?: number;
    unreleased_grades_count?: number;
    flagged_count?: number;
    recent_submissions?: RecentSubmission[];
    upcoming_deadlines?: UpcomingAssignmentSummary[];
    sections?: SectionSummary[];
    // admin
    users_by_role?: { student: number; instructor: number; admin: number };
    total_courses?: number;
    total_submissions?: number;
    total_grades_released?: number;
}

const props = defineProps<Props>();

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
            <h2 class="text-xl font-semibold leading-tight text-[#141b2b]">Dashboard</h2>
        </template>

        <div class="py-12">
            <!-- Greeting banner -->
            <div class="mx-auto mb-8 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-xl bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] px-8 py-6 text-white"
                >
                    <!-- Decorative orbs -->
                    <div
                        class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl decorative"
                        aria-hidden="true"
                    />
                    <div
                        class="pointer-events-none absolute -bottom-8 left-1/3 h-24 w-24 rounded-full bg-cyan-300/20 blur-2xl decorative"
                        aria-hidden="true"
                    />
                    <div class="relative">
                        <p class="text-xl font-semibold">{{ greeting }}, {{ userName }}!</p>
                        <p class="mt-1 text-sm text-white/80">
                            Here's your Eduno overview for today.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Student Dashboard -->
                <div v-if="props.role === 'student'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="student-stats-heading">
                        <h2 id="student-stats-heading" class="sr-only">Your statistics</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <StatCard
                                label="Enrolled Courses"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ props.enrolled_courses_count ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Upcoming Assignments"
                                :icon="ClipboardDocumentListIcon"
                                :animation-delay="80"
                                accent="amber"
                            >
                                {{ props.upcoming?.length ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Latest Grade"
                                :icon="ChartBarIcon"
                                :animation-delay="160"
                                accent="green"
                            >
                                <template v-if="props.latest_grade">
                                    {{ props.latest_grade.score }} /
                                    {{ props.latest_grade.max_score }}
                                </template>
                                <span v-else class="text-lg text-gray-400 dark:text-slate-500"
                                    >None yet</span
                                >
                            </StatCard>
                        </div>
                    </section>

                    <!-- Upcoming Assignments -->
                    <section
                        aria-labelledby="upcoming-assignments-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2
                                id="upcoming-assignments-heading"
                                class="font-semibold text-[#141b2b]"
                            >
                                Upcoming Assignments (next 7 days)
                            </h2>
                        </div>
                        <div v-if="(props.upcoming?.length ?? 0) > 0" class="">
                            <div
                                v-for="assignment in props.upcoming"
                                :key="assignment.id"
                                class="flex items-center justify-between px-6 py-3 text-sm transition-colors hover:bg-[#f1f3ff]"
                            >
                                <span class="flex items-center gap-3 font-medium text-[#141b2b]">
                                    <ClockIcon
                                        class="h-4 w-4 shrink-0 text-[#737686]"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="rounded-full bg-[#dbe1ff] px-2 py-0.5 font-mono text-xs text-[#00174b]"
                                    >
                                        {{ assignment.course_code }}
                                    </span>
                                    {{ assignment.title }}
                                </span>
                                <time
                                    :datetime="assignment.due_at ?? ''"
                                    class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700"
                                >
                                    {{ formatDate(assignment.due_at) }}
                                </time>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-[#737686]">
                            No assignments due in the next 7 days.
                        </p>
                    </section>

                    <!-- Recent Announcements -->
                    <section
                        aria-labelledby="recent-announcements-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center justify-between bg-[#f1f3ff] px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                                <h2
                                    id="recent-announcements-heading"
                                    class="font-semibold text-[#141b2b]"
                                >
                                    Recent Announcements
                                </h2>
                            </div>
                            <Link
                                :href="route('student.announcements.index')"
                                class="rounded text-sm text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                View all
                            </Link>
                        </div>
                        <div v-if="(props.recent_announcements?.length ?? 0) > 0" class="">
                            <div
                                v-for="announcement in props.recent_announcements"
                                :key="announcement.id"
                                class="flex items-start gap-3 px-6 py-3 transition-colors hover:bg-[#f1f3ff]"
                            >
                                <MegaphoneIcon
                                    class="mt-0.5 h-4 w-4 shrink-0 text-[#004ac6]"
                                    aria-hidden="true"
                                />
                                <div>
                                    <p class="text-sm font-medium text-[#141b2b]">
                                        {{ announcement.title }}
                                    </p>
                                    <p
                                        class="mt-0.5 flex items-center gap-1.5 text-xs text-[#434655]"
                                    >
                                        <span
                                            class="rounded-full bg-[#dbe1ff] px-2 py-0.5 text-xs font-medium text-[#00174b]"
                                        >
                                            {{ announcement.course_section.course.code }}
                                        </span>
                                        {{ announcement.author.name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-[#737686]">
                            No recent announcements.
                        </p>
                    </section>

                    <!-- Recent Grades -->
                    <section
                        v-if="(props.recent_grades?.length ?? 0) > 0"
                        aria-labelledby="recent-grades-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center justify-between bg-[#f1f3ff] px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-4 w-1 rounded-full bg-green-500" aria-hidden="true" />
                                <h2 id="recent-grades-heading" class="font-semibold text-[#141b2b]">
                                    Recent Grades
                                </h2>
                            </div>
                            <Link
                                :href="route('student.grades.index')"
                                class="rounded text-sm text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                View all
                            </Link>
                        </div>
                        <div class="">
                            <div
                                v-for="(grade, idx) in props.recent_grades"
                                :key="idx"
                                class="flex items-center justify-between px-6 py-3 text-sm transition-colors hover:bg-[#f1f3ff]"
                            >
                                <div>
                                    <p class="font-medium text-[#141b2b]">
                                        {{ grade.assignment_title }}
                                    </p>
                                    <p class="text-xs text-[#434655]">
                                        {{ grade.course_name }}
                                    </p>
                                </div>
                                <span class="font-semibold text-[#141b2b]">
                                    {{ grade.score }} / {{ grade.max_score }}
                                </span>
                            </div>
                        </div>
                    </section>

                    <!-- My Courses -->
                    <section
                        v-if="(props.course_summary?.length ?? 0) > 0"
                        aria-labelledby="my-courses-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center justify-between bg-[#f1f3ff] px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                                <h2 id="my-courses-heading" class="font-semibold text-[#141b2b]">
                                    My Courses
                                </h2>
                            </div>
                            <Link
                                :href="route('student.courses.index')"
                                class="rounded text-sm text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                View all
                            </Link>
                        </div>
                        <ul class="" role="list">
                            <li
                                v-for="section in props.course_summary"
                                :key="section.id"
                                class="flex items-center gap-3 px-6 py-3 transition-colors hover:bg-[#f1f3ff]"
                            >
                                <BookOpenIcon
                                    class="h-4 w-4 shrink-0 text-[#737686]"
                                    aria-hidden="true"
                                />
                                <span class="text-sm text-[#141b2b]">
                                    <span class="font-mono font-medium">{{ section.code }}</span>
                                    — {{ section.title }}
                                    <span class="text-[#434655]">({{ section.section_name }})</span>
                                </span>
                            </li>
                        </ul>
                    </section>
                </div>

                <!-- Instructor Dashboard -->
                <div v-else-if="props.role === 'instructor'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="instructor-stats-heading">
                        <h2 id="instructor-stats-heading" class="sr-only">Your statistics</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <StatCard
                                label="Course Sections"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ props.courses_count ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Pending Submissions"
                                :icon="ClipboardDocumentListIcon"
                                :animation-delay="80"
                                accent="cyan"
                            >
                                {{ props.pending_submissions_count ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Unreleased Grades"
                                :icon="ChartBarIcon"
                                :animation-delay="160"
                                accent="amber"
                            >
                                {{ props.unreleased_grades_count ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Flagged Submissions"
                                :icon="FlagIcon"
                                :animation-delay="240"
                                accent="red"
                            >
                                {{ props.flagged_count ?? 0 }}
                            </StatCard>
                        </div>
                    </section>

                    <!-- Unreleased grades reminder banner -->
                    <div
                        v-if="(props.unreleased_grades_count ?? 0) > 0"
                        class="flex items-start gap-3 rounded-xl bg-amber-50 px-5 py-4"
                        role="status"
                        aria-live="polite"
                    >
                        <ExclamationTriangleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-amber-500"
                            aria-hidden="true"
                        />
                        <div class="flex-1 text-sm text-amber-800">
                            <span class="font-semibold">
                                {{ props.unreleased_grades_count }}
                                {{ props.unreleased_grades_count === 1 ? 'grade' : 'grades' }}
                                waiting to be released.
                            </span>
                            Students cannot see their scores until you release them.
                        </div>
                        <Link
                            :href="route('instructor.submissions.all')"
                            class="shrink-0 rounded text-sm font-medium text-amber-700 underline hover:text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1"
                        >
                            View Submissions →
                        </Link>
                    </div>

                    <!-- My Sections -->
                    <section
                        v-if="(props.sections?.length ?? 0) > 0"
                        aria-labelledby="instructor-sections-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2
                                id="instructor-sections-heading"
                                class="font-semibold text-[#141b2b]"
                            >
                                My Sections
                            </h2>
                        </div>
                        <ul class="" role="list">
                            <li
                                v-for="section in props.sections"
                                :key="section.id"
                                class="px-6 py-4 transition-colors hover:bg-[#f1f3ff]"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-[#141b2b]">
                                        <span class="font-mono">{{ section.course.code }}</span>
                                        — {{ section.section_name }}
                                    </span>
                                    <span class="flex gap-4 text-xs text-[#434655]">
                                        <span>{{ section.enrollments_count }} enrolled</span>
                                        <span>{{ section.assignments_count }} assignments</span>
                                    </span>
                                </div>
                                <!-- Quick-action links -->
                                <div class="mt-2 flex gap-4">
                                    <Link
                                        :href="
                                            route('instructor.courses.modules.index', section.id)
                                        "
                                        class="inline-flex items-center gap-1 rounded text-xs font-medium text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1"
                                        :aria-label="`Go to Modules for ${section.section_name}`"
                                    >
                                        <BookOpenIcon class="h-3.5 w-3.5" aria-hidden="true" />
                                        Modules
                                    </Link>
                                    <Link
                                        :href="route('instructor.roster.index', section.id)"
                                        class="inline-flex items-center gap-1 rounded text-xs font-medium text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1"
                                        :aria-label="`Go to Roster for ${section.section_name}`"
                                    >
                                        <UserGroupIcon class="h-3.5 w-3.5" aria-hidden="true" />
                                        Roster
                                    </Link>
                                    <Link
                                        :href="route('instructor.gradebook.show', section.id)"
                                        class="inline-flex items-center gap-1 rounded text-xs font-medium text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1"
                                        :aria-label="`Go to Gradebook for ${section.section_name}`"
                                    >
                                        <AcademicCapIcon class="h-3.5 w-3.5" aria-hidden="true" />
                                        Gradebook
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </section>

                    <!-- Upcoming Deadlines -->
                    <section
                        aria-labelledby="instructor-deadlines-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2
                                id="instructor-deadlines-heading"
                                class="font-semibold text-[#141b2b]"
                            >
                                Upcoming Deadlines (next 7 days)
                            </h2>
                        </div>
                        <div v-if="(props.upcoming_deadlines?.length ?? 0) > 0" class="">
                            <div
                                v-for="assignment in props.upcoming_deadlines"
                                :key="assignment.id"
                                class="flex items-center justify-between px-6 py-3 text-sm transition-colors hover:bg-[#f1f3ff]"
                            >
                                <span class="flex items-start gap-3 font-medium text-[#141b2b]">
                                    <ClockIcon
                                        class="h-4 w-4 shrink-0 text-[#737686]"
                                        aria-hidden="true"
                                    />
                                    {{ assignment.title }}
                                </span>
                                <time
                                    :datetime="assignment.due_at ?? ''"
                                    class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700"
                                >
                                    {{ formatDate(assignment.due_at) }}
                                </time>
                            </div>
                        </div>
                        <p v-else class="px-6 py-4 text-sm text-[#737686]">
                            No deadlines in the next 7 days.
                        </p>
                    </section>

                    <!-- Recent Submissions -->
                    <section
                        aria-labelledby="recent-submissions-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2
                                id="recent-submissions-heading"
                                class="font-semibold text-[#141b2b]"
                            >
                                Recent Submissions
                            </h2>
                        </div>
                        <div v-if="(props.recent_submissions?.length ?? 0) > 0" class="">
                            <div
                                v-for="submission in props.recent_submissions"
                                :key="submission.id"
                                class="flex items-center justify-between px-6 py-3 text-sm transition-colors hover:bg-[#f1f3ff]"
                            >
                                <div>
                                    <p class="font-medium text-[#141b2b]">
                                        {{ submission.student.name }}
                                    </p>
                                    <p class="text-xs text-[#434655]">
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
                        <p v-else class="px-6 py-4 text-sm text-[#737686]">
                            No recent submissions.
                        </p>
                    </section>
                </div>

                <!-- Admin Dashboard -->
                <div v-else-if="props.role === 'admin'" class="space-y-8">
                    <!-- Stats -->
                    <section aria-labelledby="admin-stats-heading">
                        <h2 id="admin-stats-heading" class="sr-only">System statistics</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <StatCard
                                label="Total Courses"
                                :icon="BookOpenIcon"
                                :animation-delay="0"
                                accent="blue"
                            >
                                {{ props.total_courses ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Total Submissions"
                                :icon="ClipboardDocumentListIcon"
                                :animation-delay="80"
                                accent="cyan"
                            >
                                {{ props.total_submissions ?? 0 }}
                            </StatCard>
                            <StatCard
                                label="Grades Released"
                                :icon="ChartBarIcon"
                                value-class="text-green-600"
                                :animation-delay="160"
                                accent="green"
                            >
                                {{ props.total_grades_released ?? 0 }}
                            </StatCard>
                        </div>
                    </section>

                    <!-- Users by Role -->
                    <section
                        aria-labelledby="users-by-role-heading"
                        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3 bg-[#f1f3ff] px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true" />
                            <h2 id="users-by-role-heading" class="font-semibold text-[#141b2b]">
                                Users by Role
                            </h2>
                        </div>
                        <dl class="grid grid-cols-3 gap-px bg-[#e9edff]">
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 0ms`"
                                class="bg-white px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-[#737686]"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-[#434655]">Students</dt>
                                <dd class="mt-1 text-2xl font-semibold text-[#141b2b]">
                                    {{ props.users_by_role?.student ?? 0 }}
                                </dd>
                            </div>
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 80ms`"
                                class="bg-white px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-[#737686]"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-[#434655]">Instructors</dt>
                                <dd class="mt-1 text-2xl font-semibold text-[#141b2b]">
                                    {{ props.users_by_role?.instructor ?? 0 }}
                                </dd>
                            </div>
                            <div
                                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                                :style="`animation-delay: 160ms`"
                                class="bg-white px-6 py-5 text-center"
                            >
                                <UsersIcon
                                    class="mx-auto mb-2 h-5 w-5 text-[#737686]"
                                    aria-hidden="true"
                                />
                                <dt class="text-sm font-medium text-[#434655]">Admins</dt>
                                <dd class="mt-1 text-2xl font-semibold text-[#141b2b]">
                                    {{ props.users_by_role?.admin ?? 0 }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <!-- Quick Actions -->
                    <section aria-labelledby="quick-actions-heading">
                        <h2 id="quick-actions-heading" class="mb-4 font-semibold text-[#141b2b]">
                            Quick Actions
                        </h2>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <Link
                                :href="route('admin.users.index')"
                                class="group flex flex-col items-center gap-3 rounded-xl bg-white px-4 py-6 text-center text-sm font-medium text-[#141b2b] transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#f1f3ff] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff] transition-colors group-hover:bg-[#e1e8fd]"
                                >
                                    <UsersIcon class="h-6 w-6 text-[#004ac6]" aria-hidden="true" />
                                </div>
                                Manage Users
                            </Link>
                            <Link
                                :href="route('admin.courses.index')"
                                class="group flex flex-col items-center gap-3 rounded-xl bg-white px-4 py-6 text-center text-sm font-medium text-[#141b2b] transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#f1f3ff] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff] transition-colors group-hover:bg-[#e1e8fd]"
                                >
                                    <BookOpenIcon
                                        class="h-6 w-6 text-[#004ac6]"
                                        aria-hidden="true"
                                    />
                                </div>
                                Manage Courses
                            </Link>
                            <Link
                                :href="route('admin.reports.index')"
                                class="group flex flex-col items-center gap-3 rounded-xl bg-white px-4 py-6 text-center text-sm font-medium text-[#141b2b] transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#f1f3ff] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff] transition-colors group-hover:bg-[#e1e8fd]"
                                >
                                    <ChartBarIcon
                                        class="h-6 w-6 text-[#004ac6]"
                                        aria-hidden="true"
                                    />
                                </div>
                                View Reports
                            </Link>
                            <Link
                                :href="route('admin.audit-logs.index')"
                                class="group flex flex-col items-center gap-3 rounded-xl bg-white px-4 py-6 text-center text-sm font-medium text-[#141b2b] transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#f1f3ff] focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                            >
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff] transition-colors group-hover:bg-[#e1e8fd]"
                                >
                                    <ClipboardDocumentListIcon
                                        class="h-6 w-6 text-[#004ac6]"
                                        aria-hidden="true"
                                    />
                                </div>
                                Audit Logs
                            </Link>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
