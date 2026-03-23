<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    BookOpenIcon,
    MegaphoneIcon,
    UsersIcon,
    TableCellsIcon,
    ClipboardDocumentListIcon,
    FlagIcon,
} from '@heroicons/vue/24/outline';

interface ResourceItem {
    id: number;
    title: string;
    mime_type: string;
    size_bytes: number;
}

interface LessonItem {
    id: number;
    title: string;
    type: string;
    order_no: number;
    published_at: string | null;
    resources: ResourceItem[];
}

interface ModuleItem {
    id: number;
    title: string;
    description: string | null;
    order_no: number;
    published_at: string | null;
    lessons: LessonItem[];
}

interface AssignmentItem {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    published_at: string | null;
    submissions: Array<{
        id: number;
        student_id: number;
        grade?: { score: number; released_at: string | null } | null;
    }>;
}

interface AnnouncementItem {
    id: number;
    title: string;
    body: string;
    published_at: string | null;
}

interface Student {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

interface GradebookCell {
    score: number | null;
    max_score: number;
    released: boolean;
    submission_id: number | null;
}

interface Section {
    id: number;
    section_name: string;
    course: { id: number; code: string; title: string };
    modules: ModuleItem[];
    assignments: AssignmentItem[];
    announcements: AnnouncementItem[];
}

const props = defineProps<{
    section: Section;
    students: Student[];
    assignments: AssignmentItem[];
    gradebook: Record<number, Record<number, GradebookCell>>;
}>();

// ── Tab management ────────────────────────────────────────────────────────────
type Tab = 'stream' | 'classwork' | 'people' | 'grades';

const urlParams = new URLSearchParams(window.location.search);
const activeTab = ref<Tab>((urlParams.get('tab') as Tab) ?? 'stream');

function setTab(tab: Tab): void {
    activeTab.value = tab;
    router.get(
        route('instructor.courses.modules.index', props.section.id),
        { tab },
        { replace: true, preserveScroll: true, preserveState: true }
    );
}

const tabs: { key: Tab; label: string; icon: typeof BookOpenIcon }[] = [
    { key: 'stream', label: 'Stream', icon: MegaphoneIcon },
    { key: 'classwork', label: 'Classwork', icon: ClipboardDocumentListIcon },
    { key: 'people', label: 'People', icon: UsersIcon },
    { key: 'grades', label: 'Grades', icon: TableCellsIcon },
];

// ── Confirm dialog ────────────────────────────────────────────────────────────
interface ConfirmState {
    visible: boolean;
    message: string;
    onConfirm: () => void;
}

const confirm = ref<ConfirmState>({ visible: false, message: '', onConfirm: () => {} });

function openConfirm(message: string, action: () => void): void {
    confirm.value = { visible: true, message, onConfirm: action };
}

function closeConfirm(): void {
    confirm.value = { visible: false, message: '', onConfirm: () => {} };
}

function executeConfirm(): void {
    confirm.value.onConfirm();
    closeConfirm();
}

// ── Module / Lesson / Resource actions ───────────────────────────────────────
const deleteModule = (moduleId: number): void => {
    openConfirm('Delete this module and all its lessons?', () => {
        router.delete(
            route('instructor.courses.modules.destroy', {
                section: props.section.id,
                module: moduleId,
            })
        );
    });
};

const deleteLesson = (moduleId: number, lessonId: number): void => {
    openConfirm('Delete this lesson?', () => {
        router.delete(
            route('instructor.courses.modules.lessons.destroy', {
                section: props.section.id,
                module: moduleId,
                lesson: lessonId,
            })
        );
    });
};

const deleteResource = (moduleId: number, lessonId: number, resourceId: number): void => {
    openConfirm('Delete this resource file?', () => {
        router.delete(
            route('instructor.courses.modules.lessons.resources.destroy', {
                section: props.section.id,
                module: moduleId,
                lesson: lessonId,
                resource: resourceId,
            })
        );
    });
};

// ── Announcement actions ──────────────────────────────────────────────────────
const announcementDeleteForm = useForm({});
const announcementConfirmTarget = ref<number | null>(null);

function requestDeleteAnnouncement(id: number): void {
    announcementConfirmTarget.value = id;
}

function confirmDeleteAnnouncement(): void {
    if (announcementConfirmTarget.value === null) return;
    announcementDeleteForm.delete(
        route('instructor.announcements.destroy', announcementConfirmTarget.value)
    );
    announcementConfirmTarget.value = null;
}

// ── People tab ───────────────────────────────────────────────────────────────
const unenrollForm = useForm<{ user_id: number | null }>({ user_id: null });

function unenrollStudent(studentId: number, studentName: string): void {
    openConfirm(`Remove ${studentName} from this section?`, () => {
        unenrollForm.user_id = studentId;
        router.delete(route('sections.unenroll', props.section.id), {
            data: { user_id: studentId },
        });
    });
}

// ── Grades tab helpers ───────────────────────────────────────────────────────
const formatDate = (dateString: string | null): string => {
    if (!dateString) return '—';
    return new Intl.DateTimeFormat('en-PH', {
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));
};

const submissionCount = computed(
    () => (assignment: AssignmentItem) => assignment.submissions?.length ?? 0
);
</script>

<template>
    <Head :title="`${section.course.code} — ${section.section_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <Breadcrumb
                    :crumbs="[
                        { label: 'My Courses', href: route('instructor.courses.index') },
                        { label: `${section.course.code} — ${section.section_name}` },
                    ]"
                />
                <Link
                    :href="route('instructor.courses.modules.create', section.id)"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <PlusIcon class="h-4 w-4" aria-hidden="true" />
                    Add Module
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Course header -->
            <div
                class="mb-6 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-5 text-white shadow-sm"
            >
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-100">
                    {{ section.course.code }}
                </p>
                <h1 class="mt-1 text-xl font-bold">{{ section.course.title }}</h1>
                <p class="mt-0.5 text-sm text-blue-100">{{ section.section_name }}</p>
            </div>

            <!-- Tab bar -->
            <div
                class="mb-6 flex gap-1 overflow-hidden rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-100"
                role="tablist"
                aria-label="Section tabs"
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
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Announcements</h2>
                    <Link
                        :href="
                            route('instructor.announcements.create') + `?section_id=${section.id}`
                        "
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <PlusIcon class="h-4 w-4" aria-hidden="true" />
                        New Announcement
                    </Link>
                </div>

                <div
                    v-if="section.announcements.length === 0"
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
                        v-for="announcement in section.announcements"
                        :key="announcement.id"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                    >
                        <article :aria-labelledby="`ann-${announcement.id}-title`">
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50"
                                        >
                                            <MegaphoneIcon
                                                class="h-4 w-4 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div>
                                            <h3
                                                :id="`ann-${announcement.id}-title`"
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ announcement.title }}
                                            </h3>
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                <span
                                                    class="inline-block rounded-full px-2 py-0.5 text-xs"
                                                    :class="
                                                        announcement.published_at
                                                            ? 'bg-green-100 text-green-700'
                                                            : 'bg-yellow-100 text-yellow-700'
                                                    "
                                                >
                                                    {{
                                                        announcement.published_at
                                                            ? 'Published'
                                                            : 'Draft'
                                                    }}
                                                </span>
                                                <span v-if="announcement.published_at" class="ml-1">
                                                    &middot;
                                                    {{ formatDate(announcement.published_at) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 gap-3 text-sm">
                                        <Link
                                            :href="
                                                route(
                                                    'instructor.announcements.edit',
                                                    announcement.id
                                                )
                                            "
                                            class="rounded text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :aria-label="`Edit ${announcement.title}`"
                                        >
                                            Edit
                                        </Link>
                                        <template
                                            v-if="announcementConfirmTarget === announcement.id"
                                        >
                                            <span class="text-gray-600">Delete?</span>
                                            <button
                                                type="button"
                                                class="rounded text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                :aria-busy="announcementDeleteForm.processing"
                                                @click="confirmDeleteAnnouncement"
                                            >
                                                Yes
                                            </button>
                                            <button
                                                type="button"
                                                class="rounded text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                @click="announcementConfirmTarget = null"
                                            >
                                                No
                                            </button>
                                        </template>
                                        <button
                                            v-else
                                            type="button"
                                            class="rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                            :aria-label="`Delete ${announcement.title}`"
                                            @click="requestDeleteAnnouncement(announcement.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-3 line-clamp-2 text-sm text-gray-600">
                                    {{ announcement.body }}
                                </p>
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
            >
                <!-- Modules section -->
                <section aria-labelledby="modules-heading" class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 id="modules-heading" class="text-base font-semibold text-gray-800">
                            Modules
                        </h2>
                        <Link
                            :href="route('instructor.courses.modules.create', section.id)"
                            class="inline-flex items-center gap-1 rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-blue-600 ring-1 ring-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <PlusIcon class="h-4 w-4" aria-hidden="true" />
                            Add Module
                        </Link>
                    </div>

                    <EmptyState
                        v-if="section.modules.length === 0"
                        :icon="BookOpenIcon"
                        title="No modules yet."
                        description="Create your first module to get started."
                    >
                        <Link
                            :href="route('instructor.courses.modules.create', section.id)"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <PlusIcon class="h-4 w-4" aria-hidden="true" />
                            Add Module
                        </Link>
                    </EmptyState>

                    <div v-else class="space-y-4">
                        <article
                            v-for="module in section.modules"
                            :key="module.id"
                            class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                            :aria-labelledby="`module-title-${module.id}`"
                        >
                            <header
                                class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-5 py-3"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                    >
                                        {{ module.order_no }}
                                    </span>
                                    <div>
                                        <h3
                                            :id="`module-title-${module.id}`"
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ module.title }}
                                        </h3>
                                        <span
                                            class="inline-block rounded-full px-2 py-0.5 text-xs"
                                            :class="
                                                module.published_at
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-yellow-100 text-yellow-700'
                                            "
                                        >
                                            {{ module.published_at ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <Link
                                        :href="
                                            route('instructor.courses.modules.lessons.create', {
                                                section: section.id,
                                                module: module.id,
                                            })
                                        "
                                        class="inline-flex items-center gap-1 rounded text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`Add lesson to ${module.title}`"
                                    >
                                        <PlusIcon class="h-4 w-4" aria-hidden="true" />
                                        Lesson
                                    </Link>
                                    <Link
                                        :href="
                                            route('instructor.courses.modules.edit', {
                                                section: section.id,
                                                module: module.id,
                                            })
                                        "
                                        class="inline-flex items-center gap-1 rounded text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`Edit module ${module.title}`"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Delete module ${module.title}`"
                                        @click="deleteModule(module.id)"
                                    >
                                        <TrashIcon class="h-4 w-4" aria-hidden="true" />
                                        Delete
                                    </button>
                                </div>
                            </header>

                            <div v-if="module.lessons.length > 0" class="divide-y divide-gray-100">
                                <div
                                    v-for="lesson in module.lessons"
                                    :key="lesson.id"
                                    class="px-5 py-3"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-medium text-gray-800">{{
                                                lesson.title
                                            }}</span>
                                            <span
                                                class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                                                >{{ lesson.type }}</span
                                            >
                                            <span
                                                class="rounded-full px-2 py-0.5 text-xs"
                                                :class="
                                                    lesson.published_at
                                                        ? 'bg-green-100 text-green-700'
                                                        : 'bg-yellow-100 text-yellow-700'
                                                "
                                            >
                                                {{ lesson.published_at ? 'Published' : 'Draft' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm">
                                            <Link
                                                :href="
                                                    route(
                                                        'instructor.courses.modules.lessons.resources.create',
                                                        {
                                                            section: section.id,
                                                            module: module.id,
                                                            lesson: lesson.id,
                                                        }
                                                    )
                                                "
                                                class="rounded text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :aria-label="`Upload resource for ${lesson.title}`"
                                            >
                                                + Resource
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'instructor.courses.modules.lessons.edit',
                                                        {
                                                            section: section.id,
                                                            module: module.id,
                                                            lesson: lesson.id,
                                                        }
                                                    )
                                                "
                                                class="inline-flex items-center gap-1 rounded text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                :aria-label="`Edit lesson ${lesson.title}`"
                                            >
                                                <PencilSquareIcon
                                                    class="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                :aria-label="`Delete lesson ${lesson.title}`"
                                                @click="deleteLesson(module.id, lesson.id)"
                                            >
                                                <TrashIcon class="h-4 w-4" aria-hidden="true" />
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <ul
                                        v-if="(lesson.resources ?? []).length > 0"
                                        class="mt-2 space-y-1 ps-4"
                                        aria-label="Lesson resources"
                                    >
                                        <li
                                            v-for="resource in lesson.resources"
                                            :key="resource.id"
                                            class="flex items-center justify-between text-xs text-gray-600"
                                        >
                                            <span>{{ resource.title }}</span>
                                            <button
                                                type="button"
                                                class="rounded text-red-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                :aria-label="`Delete resource ${resource.title}`"
                                                @click="
                                                    deleteResource(
                                                        module.id,
                                                        lesson.id,
                                                        resource.id
                                                    )
                                                "
                                            >
                                                Remove
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-else class="px-5 py-3 text-sm text-gray-400">
                                No lessons yet.
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Assignments section -->
                <section aria-labelledby="assignments-heading">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 id="assignments-heading" class="text-base font-semibold text-gray-800">
                            Assignments
                        </h2>
                        <Link
                            :href="route('instructor.courses.assignments.create', section.id)"
                            class="inline-flex items-center gap-1 rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-blue-600 ring-1 ring-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <PlusIcon class="h-4 w-4" aria-hidden="true" />
                            Add Assignment
                        </Link>
                    </div>

                    <div
                        v-if="assignments.length === 0"
                        class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center"
                    >
                        <p class="text-sm text-gray-500">No published assignments yet.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div
                            v-for="assignment in assignments"
                            :key="assignment.id"
                            class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                        >
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-medium text-gray-900">
                                        {{ assignment.title }}
                                    </h3>
                                    <Link
                                        :href="route('instructor.submissions.index', assignment.id)"
                                        class="shrink-0 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`${submissionCount(assignment)} submissions for ${assignment.title}`"
                                    >
                                        {{ submissionCount(assignment) }} submitted
                                    </Link>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Due: {{ formatDate(assignment.due_at) }} &middot;
                                    {{ assignment.max_score }} pts
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ── PEOPLE TAB ─────────────────────────────────────────── -->
            <div
                v-show="activeTab === 'people'"
                id="tabpanel-people"
                role="tabpanel"
                aria-labelledby="tab-people"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">
                        Enrolled Students
                        <span
                            class="ml-2 inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700"
                        >
                            {{ students.length }}
                        </span>
                    </h2>
                </div>

                <div
                    v-if="students.length === 0"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center"
                >
                    <UsersIcon class="mx-auto mb-3 h-10 w-10 text-gray-300" aria-hidden="true" />
                    <p class="text-sm text-gray-500">No students enrolled yet.</p>
                </div>

                <div
                    v-else
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <table
                        class="min-w-full divide-y divide-gray-100"
                        aria-label="Enrolled students"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Student
                                </th>
                                <th
                                    scope="col"
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Email
                                </th>
                                <th
                                    scope="col"
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Enrolled
                                </th>
                                <th
                                    scope="col"
                                    class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="student in students" :key="student.id">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                                            aria-hidden="true"
                                        >
                                            {{ student.name.charAt(0).toUpperCase() }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900">{{
                                            student.name
                                        }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ student.email }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">
                                    {{ formatDate(student.created_at) }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button
                                        type="button"
                                        class="rounded text-sm text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        :aria-label="`Remove ${student.name} from section`"
                                        @click="unenrollStudent(student.id, student.name)"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── GRADES TAB ─────────────────────────────────────────── -->
            <div
                v-show="activeTab === 'grades'"
                id="tabpanel-grades"
                role="tabpanel"
                aria-labelledby="tab-grades"
            >
                <h2 class="mb-4 text-base font-semibold text-gray-800">Gradebook</h2>

                <div
                    v-if="students.length === 0 || assignments.length === 0"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center"
                >
                    <TableCellsIcon
                        class="mx-auto mb-3 h-10 w-10 text-gray-300"
                        aria-hidden="true"
                    />
                    <p class="text-sm text-gray-500">
                        {{
                            students.length === 0
                                ? 'No students enrolled.'
                                : 'No published assignments.'
                        }}
                    </p>
                </div>

                <div
                    v-else
                    class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <table class="min-w-full text-sm" aria-label="Gradebook">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="sticky left-0 bg-gray-50 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Student
                                </th>
                                <th
                                    v-for="assignment in assignments"
                                    :key="assignment.id"
                                    scope="col"
                                    class="max-w-[120px] truncate px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    :title="assignment.title"
                                >
                                    {{ assignment.title }}
                                    <span
                                        class="block text-xs font-normal normal-case text-gray-400"
                                    >
                                        Due {{ formatDate(assignment.due_at) }}
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="student in students" :key="student.id">
                                <td
                                    class="sticky left-0 bg-white px-5 py-3 font-medium text-gray-900"
                                >
                                    {{ student.name }}
                                </td>
                                <td
                                    v-for="assignment in assignments"
                                    :key="assignment.id"
                                    class="px-4 py-3 text-center"
                                >
                                    <template
                                        v-if="gradebook[student.id]?.[assignment.id]?.submission_id"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'instructor.submissions.show',
                                                    gradebook[student.id][assignment.id]
                                                        .submission_id!
                                                )
                                            "
                                            class="rounded font-medium focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="
                                                gradebook[student.id][assignment.id].score === null
                                                    ? 'text-gray-400'
                                                    : gradebook[student.id][assignment.id].released
                                                      ? 'text-green-700'
                                                      : 'text-amber-600'
                                            "
                                            :aria-label="`${student.name} — ${assignment.title}: ${gradebook[student.id][assignment.id].score ?? 'ungraded'} / ${assignment.max_score}${!gradebook[student.id][assignment.id].released ? ' (unreleased)' : ''}`"
                                        >
                                            <span
                                                v-if="
                                                    gradebook[student.id][assignment.id].score !==
                                                    null
                                                "
                                            >
                                                {{ gradebook[student.id][assignment.id].score }}
                                                / {{ assignment.max_score }}
                                            </span>
                                            <span v-else>Submitted</span>
                                        </Link>
                                    </template>
                                    <span v-else class="text-gray-300">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="mt-3 flex items-center gap-4 text-xs text-gray-500" role="note">
                    <span class="flex items-center gap-1">
                        <FlagIcon class="h-3.5 w-3.5 text-amber-500" aria-hidden="true" />
                        Amber = graded but unreleased
                    </span>
                    <span class="flex items-center gap-1">
                        <FlagIcon class="h-3.5 w-3.5 text-green-600" aria-hidden="true" />
                        Green = released to student
                    </span>
                </div>
            </div>
        </div>

        <!-- Confirm dialog -->
        <div
            v-if="confirm.visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-dialog-title"
        >
            <div class="mx-4 max-w-sm rounded-xl bg-white p-6 shadow-xl ring-1 ring-gray-200">
                <h2 id="confirm-dialog-title" class="font-semibold text-gray-900">Confirm</h2>
                <p class="mt-2 text-sm text-gray-600">{{ confirm.message }}</p>
                <div class="mt-5 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @click="closeConfirm"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                        @click="executeConfirm"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
