<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ChevronDownIcon,
    MegaphoneIcon,
    UsersIcon,
    ClipboardDocumentListIcon,
} from '@heroicons/vue/24/outline';

interface Resource {
    id: number;
    title: string;
    mime_type: string;
    size_bytes: number;
    accessibility_notes: string | null;
    reading_time_minutes: number | null;
}

interface Lesson {
    id: number;
    title: string;
    type: string;
    resources: Resource[];
}

interface CourseModule {
    id: number;
    title: string;
    order_no: number;
    lessons: Lesson[];
}

interface Submission {
    id: number;
    status: string;
    grade?: { score: number; released_at: string | null } | null;
}

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    course_section_id: number;
    submissions?: Submission[];
}

interface Announcement {
    id: number;
    title: string;
    body: string;
    published_at: string | null;
}

interface Section {
    id: number;
    section_name: string;
    schedule_text: string | null;
    course: { id: number; code: string; title: string; description: string | null };
    instructor: { id: number; name: string; email: string };
    modules: CourseModule[];
    enrollments: { id: number }[];
}

const props = defineProps<{
    section: Section;
    announcements: Announcement[];
    assignments: Assignment[];
}>();

// ── Tab management ────────────────────────────────────────────────────────────
type Tab = 'stream' | 'classwork' | 'people';

const urlParams = new URLSearchParams(window.location.search);
const activeTab = ref<Tab>((urlParams.get('tab') as Tab) ?? 'stream');

function setTab(tab: Tab): void {
    activeTab.value = tab;
    router.get(
        route('student.courses.show', props.section.id),
        { tab },
        { replace: true, preserveScroll: true, preserveState: true }
    );
}

const tabs: { key: Tab; label: string; icon: typeof MegaphoneIcon }[] = [
    { key: 'stream', label: 'Stream', icon: MegaphoneIcon },
    { key: 'classwork', label: 'Classwork', icon: ClipboardDocumentListIcon },
    { key: 'people', label: 'People', icon: UsersIcon },
];

// ── Classwork helpers ─────────────────────────────────────────────────────────
const sortedModules = computed<CourseModule[]>(() =>
    [...props.section.modules].sort((a, b) => a.order_no - b.order_no)
);

const searchQuery = ref('');

const filteredModules = computed<CourseModule[]>(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return sortedModules.value;
    return sortedModules.value.filter(
        (m) =>
            m.title.toLowerCase().includes(q) ||
            m.lessons.some((l) => l.title.toLowerCase().includes(q))
    );
});

const expandedModules = ref<Set<number>>(new Set(sortedModules.value.slice(0, 1).map((m) => m.id)));

function toggleModule(id: number): void {
    if (expandedModules.value.has(id)) {
        expandedModules.value.delete(id);
    } else {
        expandedModules.value.add(id);
    }
}

const lessonTypeLabel = (type: string): string => {
    const labels: Record<string, string> = {
        video: 'Video',
        document: 'Document',
        quiz: 'Quiz',
        text: 'Text',
    };
    return labels[type] ?? type;
};

const submissionStatus = (assignment: Assignment): string => {
    const sub = assignment.submissions?.[0];
    if (!sub) return 'not_submitted';
    if (sub.grade?.released_at) return 'graded';
    return sub.status;
};

const statusBadge: Record<string, string> = {
    not_submitted: 'bg-gray-100 text-gray-500',
    submitted: 'bg-blue-100 text-blue-700',
    graded: 'bg-green-100 text-green-700',
    returned: 'bg-purple-100 text-purple-700',
};

const statusLabel: Record<string, string> = {
    not_submitted: 'Not Submitted',
    submitted: 'Submitted',
    graded: 'Graded',
    returned: 'Returned',
};

// ── Date helpers ──────────────────────────────────────────────────────────────
const formatDate = (dateString: string | null): string => {
    if (!dateString) return '—';
    return new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));
};
</script>

<template>
    <Head :title="`${section.course.code} — ${section.course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'My Courses', href: route('student.courses.index') },
                    { label: section.course.code },
                ]"
            />
        </template>

        <div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Course hero -->
            <section
                aria-labelledby="course-info-heading"
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
            >
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-6">
                    <span
                        class="block text-xs font-semibold uppercase tracking-wider text-blue-100"
                    >
                        {{ section.course.code }}
                    </span>
                    <h1 id="course-info-heading" class="mt-1 text-2xl font-bold text-white">
                        {{ section.course.title }}
                    </h1>
                </div>
                <div class="px-6 py-5">
                    <p v-if="section.course.description" class="mb-4 text-sm text-gray-600">
                        {{ section.course.description }}
                    </p>
                    <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="font-medium text-gray-500">Section</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.section_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Instructor</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.instructor.name }}</dd>
                        </div>
                        <div v-if="section.schedule_text">
                            <dt class="font-medium text-gray-500">Schedule</dt>
                            <dd class="mt-0.5 text-gray-800">{{ section.schedule_text }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- Tab bar -->
            <div
                class="flex gap-1 overflow-hidden rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-100"
                role="tablist"
                aria-label="Course tabs"
            >
                <button
                    v-for="tab in tabs"
                    :id="`tab-${tab.key}`"
                    :key="tab.key"
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === tab.key"
                    :aria-controls="`tabpanel-${tab.key}`"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    :class="
                        activeTab === tab.key
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-gray-600 hover:bg-gray-50'
                    "
                    @click="setTab(tab.key)"
                >
                    <component :is="tab.icon" class="h-4 w-4" aria-hidden="true" />
                    <span>{{ tab.label }}</span>
                </button>
            </div>

            <!-- ── STREAM TAB ──────────────────────────────────────────── -->
            <div
                v-show="activeTab === 'stream'"
                id="tabpanel-stream"
                role="tabpanel"
                aria-labelledby="tab-stream"
            >
                <div
                    v-if="announcements.length === 0"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center"
                >
                    <MegaphoneIcon
                        class="mx-auto mb-3 h-10 w-10 text-gray-300"
                        aria-hidden="true"
                    />
                    <p class="text-sm text-gray-500">No announcements yet.</p>
                </div>

                <ul v-else class="space-y-4" aria-label="Announcements">
                    <li
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <article :aria-labelledby="`ann-${announcement.id}-title`">
                            <div class="px-6 py-5">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50"
                                    >
                                        <MegaphoneIcon
                                            class="h-4 w-4 text-blue-600"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <h3
                                            :id="`ann-${announcement.id}-title`"
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ announcement.title }}
                                        </h3>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            <time
                                                v-if="announcement.published_at"
                                                :datetime="announcement.published_at"
                                            >
                                                {{ formatDate(announcement.published_at) }}
                                            </time>
                                        </p>
                                        <p class="mt-3 text-sm text-gray-600">
                                            {{ announcement.body }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </li>
                </ul>
            </div>

            <!-- ── CLASSWORK TAB ──────────────────────────────────────── -->
            <div
                v-show="activeTab === 'classwork'"
                id="tabpanel-classwork"
                role="tabpanel"
                aria-labelledby="tab-classwork"
                class="space-y-8"
            >
                <!-- Modules -->
                <section aria-labelledby="modules-heading">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h2 id="modules-heading" class="text-lg font-semibold text-gray-800">
                            Course Modules
                        </h2>
                    </div>

                    <div role="search" class="mb-4">
                        <label for="material-search" class="sr-only">Search course materials</label>
                        <input
                            id="material-search"
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search modules and lessons…"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Search course materials"
                        />
                    </div>

                    <p
                        v-if="searchQuery.trim() && filteredModules.length === 0"
                        class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                        role="status"
                    >
                        No modules or lessons match your search.
                    </p>

                    <div v-else-if="filteredModules.length > 0" class="space-y-3">
                        <article
                            v-for="module in filteredModules"
                            :key="module.id"
                            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-5 py-3 text-left hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                :aria-expanded="expandedModules.has(module.id)"
                                :aria-controls="`module-content-${module.id}`"
                                @click="toggleModule(module.id)"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                    >
                                        {{ module.order_no }}
                                    </span>
                                    <h3 class="font-medium text-gray-900">{{ module.title }}</h3>
                                </div>
                                <ChevronDownIcon
                                    class="h-5 w-5 text-gray-400 transition-transform"
                                    :class="expandedModules.has(module.id) ? 'rotate-180' : ''"
                                    aria-hidden="true"
                                />
                            </button>

                            <div
                                v-show="expandedModules.has(module.id)"
                                :id="`module-content-${module.id}`"
                            >
                                <div
                                    v-if="module.lessons.length > 0"
                                    class="border-t border-gray-100 px-5 py-3"
                                >
                                    <ul class="divide-y divide-gray-100" aria-label="Lessons">
                                        <li
                                            v-for="lesson in module.lessons"
                                            :key="lesson.id"
                                            class="py-2 text-sm"
                                        >
                                            <div class="flex items-center justify-between">
                                                <Link
                                                    :href="route('student.lessons.show', lesson.id)"
                                                    class="rounded font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    {{ lesson.title }}
                                                </Link>
                                                <span
                                                    class="ms-3 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                                    aria-label="Lesson type"
                                                >
                                                    {{ lessonTypeLabel(lesson.type) }}
                                                </span>
                                            </div>
                                            <ul
                                                v-if="
                                                    lesson.resources && lesson.resources.length > 0
                                                "
                                                class="mt-1 space-y-1 ps-2"
                                                aria-label="Lesson resources"
                                            >
                                                <li
                                                    v-for="resource in lesson.resources"
                                                    :key="resource.id"
                                                    class="text-xs text-gray-500"
                                                >
                                                    <span class="font-medium text-gray-700">{{
                                                        resource.title
                                                    }}</span>
                                                    <span
                                                        v-if="resource.reading_time_minutes"
                                                        class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-600"
                                                    >
                                                        {{ resource.reading_time_minutes }} min read
                                                    </span>
                                                    <span
                                                        v-if="resource.accessibility_notes"
                                                        class="ml-1 text-gray-400"
                                                    >
                                                        — {{ resource.accessibility_notes }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div
                                    v-else
                                    class="border-t border-gray-100 px-5 py-3 text-sm text-gray-400"
                                >
                                    No lessons in this module yet.
                                </div>
                            </div>
                        </article>
                    </div>

                    <p
                        v-else
                        class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                    >
                        No modules have been published for this course yet.
                    </p>
                </section>

                <!-- Assignments -->
                <section aria-labelledby="assignments-heading">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h2 id="assignments-heading" class="text-lg font-semibold text-gray-800">
                            Assignments
                        </h2>
                    </div>

                    <div v-if="assignments.length > 0" class="space-y-3">
                        <div
                            v-for="assignment in assignments"
                            :key="assignment.id"
                            class="flex items-center justify-between overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100"
                        >
                            <div>
                                <Link
                                    :href="route('student.assignments.show', assignment.id)"
                                    class="font-medium text-gray-900 hover:text-blue-600 focus:outline-none focus:underline"
                                >
                                    {{ assignment.title }}
                                </Link>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Due {{ formatDate(assignment.due_at) }} &middot;
                                    {{ assignment.max_score }} pts
                                </p>
                            </div>
                            <span
                                class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="
                                    statusBadge[submissionStatus(assignment)] ??
                                    'bg-gray-100 text-gray-500'
                                "
                            >
                                {{
                                    statusLabel[submissionStatus(assignment)] ??
                                    submissionStatus(assignment)
                                }}
                            </span>
                        </div>
                    </div>

                    <p
                        v-else
                        class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-8 text-center text-sm text-gray-400"
                    >
                        No assignments have been published for this course yet.
                    </p>
                </section>
            </div>

            <!-- ── PEOPLE TAB ─────────────────────────────────────────── -->
            <div
                v-show="activeTab === 'people'"
                id="tabpanel-people"
                role="tabpanel"
                aria-labelledby="tab-people"
            >
                <div class="space-y-4">
                    <!-- Instructor card -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 class="font-semibold text-gray-900">Instructor</h2>
                        </div>
                        <div class="flex items-center gap-4 px-6 py-5">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700"
                                aria-hidden="true"
                            >
                                {{ section.instructor.name.charAt(0).toUpperCase() }}
                            </span>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ section.instructor.name }}
                                </p>
                                <p class="text-sm text-gray-500">{{ section.instructor.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Student count (privacy-preserving) -->
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 class="font-semibold text-gray-900">Students</h2>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{
                                    section.enrollments.length
                                }}</span>
                                student{{ section.enrollments.length === 1 ? '' : 's' }} enrolled in
                                this section.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
