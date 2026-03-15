<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { Announcement, Assignment, CourseSection, Grade, Submission } from '@/Types/models';

interface AdminReport {
    total_courses: number;
    total_sections: number;
    total_students: number;
    total_submissions: number;
    late_submissions: number;
    graded_submissions: number;
}

const props = defineProps<{
    // Student props
    enrolledSections?: CourseSection[];
    upcomingAssignments?: Assignment[];
    recentAnnouncements?: Announcement[];
    recentGrades?: Grade[];
    // Instructor props
    sections?: CourseSection[];
    pendingSubmissions?: Submission[];
    // Admin props
    report?: AdminReport;
}>();

const page = usePage<PageProps>();
const role = page.props.auth.user.role;

function formatDate(iso: string | null | undefined): string {
    if (!iso) return '—';
    return new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium' }).format(new Date(iso));
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Welcome, {{ page.props.auth.user.name }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- ── STUDENT ── -->
                <template v-if="role === 'student'">
                    <!-- Enrolled Courses -->
                    <section aria-labelledby="courses-heading" class="mb-8">
                        <h3 id="courses-heading" class="mb-3 text-lg font-semibold text-gray-900">
                            My Courses
                            <span class="ml-2 text-sm font-normal text-gray-500" role="status">
                                ({{ props.enrolledSections?.length ?? 0 }})
                            </span>
                        </h3>
                        <div
                            v-if="!props.enrolledSections?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            You are not enrolled in any courses yet.
                        </div>
                        <ul v-else class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <li
                                v-for="section in props.enrolledSections"
                                :key="section.id"
                                class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                            >
                                <Link
                                    :href="route('sections.modules.index', section.id)"
                                    class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                >
                                    {{ section.course?.title }}
                                </Link>
                                <p class="mt-1 text-sm text-gray-500">{{ section.section_name }}</p>
                            </li>
                        </ul>
                    </section>

                    <!-- Upcoming Assignments -->
                    <section aria-labelledby="assignments-heading" class="mb-8">
                        <h3
                            id="assignments-heading"
                            class="mb-3 text-lg font-semibold text-gray-900"
                        >
                            Upcoming Assignments
                        </h3>
                        <div
                            v-if="!props.upcomingAssignments?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            No assignments due in the next 7 days.
                        </div>
                        <ul
                            v-else
                            class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <li
                                v-for="assignment in props.upcomingAssignments"
                                :key="assignment.id"
                                class="flex items-center justify-between px-4 py-3"
                            >
                                <Link
                                    :href="route('assignments.show', assignment.id)"
                                    class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                >
                                    {{ assignment.title }}
                                </Link>
                                <span class="text-sm text-gray-500"
                                    >Due {{ formatDate(assignment.due_at) }}</span
                                >
                            </li>
                        </ul>
                    </section>

                    <!-- Recent Announcements (FR-018) -->
                    <section aria-labelledby="announcements-heading" class="mb-8">
                        <h3
                            id="announcements-heading"
                            class="mb-3 text-lg font-semibold text-gray-900"
                        >
                            Recent Announcements
                        </h3>
                        <div
                            v-if="!props.recentAnnouncements?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            No recent announcements.
                        </div>
                        <ul
                            v-else
                            class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <li
                                v-for="ann in props.recentAnnouncements"
                                :key="ann.id"
                                class="px-4 py-3"
                            >
                                <Link
                                    :href="route('announcements.show', ann.id)"
                                    class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                >
                                    {{ ann.title }}
                                </Link>
                                <p class="mt-0.5 text-sm text-gray-500">
                                    {{ ann.section?.course?.title }} ·
                                    {{ formatDate(ann.published_at) }}
                                </p>
                            </li>
                        </ul>
                    </section>

                    <!-- Recent Grades -->
                    <section aria-labelledby="grades-heading">
                        <h3 id="grades-heading" class="mb-3 text-lg font-semibold text-gray-900">
                            Recent Grades
                        </h3>
                        <div
                            v-if="!props.recentGrades?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            No released grades yet.
                        </div>
                        <ul
                            v-else
                            class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <li
                                v-for="grade in props.recentGrades"
                                :key="grade.id"
                                class="flex items-center justify-between px-4 py-3"
                            >
                                <Link
                                    :href="route('submissions.show', grade.submission_id)"
                                    class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                >
                                    {{ grade.submission?.assignment?.title }}
                                </Link>
                                <span class="text-sm font-medium text-gray-900" role="status">
                                    {{ grade.score }} /
                                    {{ grade.submission?.assignment?.max_score }}
                                </span>
                            </li>
                        </ul>
                    </section>
                </template>

                <!-- ── INSTRUCTOR ── -->
                <template v-else-if="role === 'instructor'">
                    <section aria-labelledby="sections-heading" class="mb-8">
                        <h3 id="sections-heading" class="mb-3 text-lg font-semibold text-gray-900">
                            My Sections
                            <span class="ml-2 text-sm font-normal text-gray-500" role="status">
                                ({{ props.sections?.length ?? 0 }})
                            </span>
                        </h3>
                        <div
                            v-if="!props.sections?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            You have no sections yet.
                        </div>
                        <ul v-else class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <li
                                v-for="section in props.sections"
                                :key="section.id"
                                class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                            >
                                <Link
                                    :href="route('sections.modules.index', section.id)"
                                    class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                >
                                    {{ section.course?.title }}
                                </Link>
                                <p class="mt-1 text-sm text-gray-500">{{ section.section_name }}</p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ section.enrollments_count }} students ·
                                    {{ section.assignments_count }} assignments
                                </p>
                            </li>
                        </ul>
                    </section>

                    <section aria-labelledby="pending-heading">
                        <h3 id="pending-heading" class="mb-3 text-lg font-semibold text-gray-900">
                            Pending Submissions
                            <span class="ml-2 text-sm font-normal text-gray-500" role="status">
                                ({{ props.pendingSubmissions?.length ?? 0 }})
                            </span>
                        </h3>
                        <div
                            v-if="!props.pendingSubmissions?.length"
                            class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
                            role="status"
                        >
                            No ungraded submissions.
                        </div>
                        <ul
                            v-else
                            class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-sm"
                        >
                            <li
                                v-for="sub in props.pendingSubmissions"
                                :key="sub.id"
                                class="flex items-center justify-between px-4 py-3"
                            >
                                <div>
                                    <Link
                                        :href="route('submissions.show', sub.id)"
                                        class="font-medium text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                                    >
                                        {{ sub.student?.name }}
                                    </Link>
                                    <p class="text-sm text-gray-500">{{ sub.assignment?.title }}</p>
                                </div>
                                <span class="text-sm text-gray-500">{{
                                    formatDate(sub.submitted_at)
                                }}</span>
                            </li>
                        </ul>
                    </section>
                </template>

                <!-- ── ADMIN ── -->
                <template v-else>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(value, key) in props.report"
                            :key="key"
                            class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                        >
                            <dt class="text-sm font-medium capitalize text-gray-500">
                                {{ String(key).replace(/_/g, ' ') }}
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900" role="status">
                                {{ value }}
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-4 text-right">
                        <Link
                            :href="route('admin.reports.index')"
                            class="text-sm text-blue-700 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600"
                        >
                            View full report →
                        </Link>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
