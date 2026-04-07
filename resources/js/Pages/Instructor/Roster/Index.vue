<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { UsersIcon, UserMinusIcon } from '@heroicons/vue/24/outline';

interface StudentRow {
    enrollment_id: number;
    id: number;
    name: string;
    email: string;
    enrolled_at: string;
}

interface SectionInfo {
    id: number;
    section_name: string;
    course?: { id: number; code: string; title: string };
}

const props = defineProps<{
    section: SectionInfo;
    students: StudentRow[];
}>();

// ── Search ────────────────────────────────────────────────────────────────────

const search = ref('');

const filteredStudents = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.students;
    return props.students.filter(
        (s) => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q)
    );
});

// ── Add student form ──────────────────────────────────────────────────────────

const enrollForm = useForm({ email: '' });

function submitEnroll(): void {
    enrollForm.post(route('instructor.roster.store', props.section.id), {
        preserveScroll: true,
        onSuccess: () => enrollForm.reset(),
    });
}

// ── Remove confirmation ───────────────────────────────────────────────────────

interface ConfirmState {
    visible: boolean;
    studentName: string;
    enrollmentId: number | null;
}

const confirm = ref<ConfirmState>({
    visible: false,
    studentName: '',
    enrollmentId: null,
});

function openConfirm(student: StudentRow): void {
    confirm.value = {
        visible: true,
        studentName: student.name,
        enrollmentId: student.enrollment_id,
    };
}

function closeConfirm(): void {
    confirm.value = { visible: false, studentName: '', enrollmentId: null };
}

const removeForm = useForm({});

function confirmRemove(): void {
    if (confirm.value.enrollmentId === null) return;
    removeForm.delete(
        route('instructor.roster.destroy', {
            section: props.section.id,
            enrollment: confirm.value.enrollmentId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => closeConfirm(),
        }
    );
}
</script>

<template>
    <Head :title="`Roster — ${props.section.course?.code ?? ''} · ${props.section.section_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'Courses', href: route('instructor.courses.index') },
                    {
                        label: `${props.section.course?.code ?? ''} — ${props.section.section_name}`,
                        href: route('instructor.courses.modules.index', props.section.id),
                    },
                    { label: 'Roster' },
                ]"
            />
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Page header -->
            <header class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Roster
                        <span
                            v-if="props.section.course"
                            class="text-base font-normal text-gray-500 dark:text-slate-400"
                        >
                            — {{ props.section.course.title }} · Section
                            {{ props.section.section_name }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                        <span class="font-mono font-medium">{{ props.section.course?.code }}</span>
                        ·
                        <span
                            class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                        >
                            {{ props.students.length }}
                            {{ props.students.length === 1 ? 'student' : 'students' }}
                        </span>
                    </p>
                </div>
            </header>

            <!-- Search -->
            <div class="mb-4">
                <label for="roster-search" class="sr-only">Search students</label>
                <input
                    id="roster-search"
                    v-model="search"
                    type="search"
                    placeholder="Search by name or email…"
                    class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:max-w-xs dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    aria-label="Search students"
                />
            </div>

            <!-- Empty state -->
            <EmptyState
                v-if="props.students.length === 0"
                :icon="UsersIcon"
                title="No students enrolled yet."
                description="Use the form below to add a student by email."
            />

            <!-- Student table -->
            <div
                v-else
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900">
                        <tr>
                            <th
                                scope="col"
                                class="py-3 pl-6 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                            >
                                Name
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                            >
                                Email
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                            >
                                Enrolled On
                            </th>
                            <th scope="col" class="relative py-3 pl-3 pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 bg-white dark:divide-slate-700 dark:bg-slate-800"
                    >
                        <tr
                            v-for="student in filteredStudents"
                            :key="student.enrollment_id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-slate-700/50"
                        >
                            <td
                                class="py-3 pl-6 pr-3 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ student.name }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-500 dark:text-slate-400">
                                {{ student.email }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-500 dark:text-slate-400">
                                {{ new Date(student.enrolled_at).toLocaleDateString() }}
                            </td>
                            <td class="py-3 pl-3 pr-6 text-right text-sm">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 rounded text-sm font-medium text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                    :aria-label="`Remove ${student.name}`"
                                    @click="openConfirm(student)"
                                >
                                    <UserMinusIcon class="h-4 w-4" aria-hidden="true" />
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td
                                colspan="4"
                                class="py-2 pl-6 text-xs text-gray-400 dark:text-slate-500"
                            >
                                {{ filteredStudents.length }}
                                of
                                {{ props.students.length }}
                                {{ props.students.length === 1 ? 'student' : 'students' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- No search results -->
                <p
                    v-if="filteredStudents.length === 0"
                    class="py-6 text-center text-sm text-gray-500 dark:text-slate-400"
                    role="status"
                >
                    No students match "{{ search }}".
                </p>
            </div>

            <!-- Add student card -->
            <section
                class="mt-8 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                aria-labelledby="add-student-heading"
            >
                <div class="border-b border-gray-100 px-6 py-4 dark:border-slate-700">
                    <h2
                        id="add-student-heading"
                        class="font-semibold text-gray-900 dark:text-white"
                    >
                        Add Student
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-slate-400">
                        Enroll a student by their registered email address.
                    </p>
                </div>

                <form class="px-6 py-5" @submit.prevent="submitEnroll">
                    <div class="flex flex-wrap items-start gap-3 sm:flex-nowrap">
                        <div class="flex-1">
                            <label
                                for="enroll-email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Student email
                            </label>
                            <input
                                id="enroll-email"
                                v-model="enrollForm.email"
                                type="email"
                                autocomplete="off"
                                placeholder="student@example.com"
                                class="mt-1 block w-full rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                :class="{ 'border-red-400': enrollForm.errors.email }"
                                :aria-describedby="
                                    enrollForm.errors.email ? 'enroll-email-error' : undefined
                                "
                                aria-required="true"
                            />
                            <p
                                v-if="enrollForm.errors.email"
                                id="enroll-email-error"
                                class="mt-1 text-xs text-red-600"
                                role="alert"
                            >
                                {{ enrollForm.errors.email }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="enrollForm.processing"
                            class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            Enroll
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Confirmation modal -->
        <div
            v-if="confirm.visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-dialog-title"
        >
            <div class="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-slate-800">
                <h3
                    id="confirm-dialog-title"
                    class="text-base font-semibold text-gray-900 dark:text-white"
                >
                    Remove student
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Remove
                    <span class="font-medium">{{ confirm.studentName }}</span>
                    from this section? They will lose access to course materials.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                        @click="closeConfirm"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="removeForm.processing"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 disabled:opacity-50"
                        @click="confirmRemove"
                    >
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
