<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head } from '@inertiajs/vue3';
import { LockClosedIcon, CheckIcon } from '@heroicons/vue/24/outline';

interface GradebookAssignment {
    id: number;
    title: string;
    max_score: number;
    due_at: string | null;
}

interface GradeCell {
    score: number;
    released: boolean;
}

interface GradebookStudent {
    id: number;
    name: string;
    grades: Record<number, GradeCell | null>;
    total: number;
}

interface Props {
    section: {
        id: number;
        section_name: string;
        course: { id: number; code: string; title: string };
    };
    assignments: GradebookAssignment[];
    students: GradebookStudent[];
    averages: Record<number, number | null>;
}

const props = defineProps<Props>();

function scorePercent(score: number, maxScore: number): number {
    return Math.min(100, Math.round((score / maxScore) * 100));
}

function cellClass(cell: GradeCell | null | undefined, maxScore: number): string {
    if (!cell) return 'text-[#737686]';
    const pct = scorePercent(cell.score, maxScore);
    if (pct >= 90) return 'text-green-700 bg-green-50';
    if (pct >= 75) return 'text-[#004ac6] bg-[#dbe1ff]';
    if (pct >= 60) return 'text-amber-700 bg-amber-50';
    return 'text-red-700 bg-red-50';
}
</script>

<template>
    <Head :title="`Gradebook — ${section.course.code} ${section.section_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :crumbs="[
                    { label: 'Courses', href: route('instructor.courses.index') },
                    {
                        label: `${section.course.code} — ${section.section_name}`,
                        href: route('instructor.courses.index'),
                    },
                    { label: 'Gradebook' },
                ]"
            />
        </template>

        <div class="mx-auto max-w-full px-4 py-8 sm:px-6 lg:px-8">
            <section aria-labelledby="gradebook-heading">
                <!-- Page header -->
                <header class="mb-6 flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1
                            id="gradebook-heading"
                            class="text-xl font-bold text-[#141b2b] dark:text-white"
                        >
                            Gradebook
                        </h1>
                        <p class="mt-1 text-sm text-[#434655] dark:text-slate-400">
                            <span class="font-mono font-medium">{{ section.course.code }}</span>
                            — {{ section.course.title }} · Section {{ section.section_name }}
                        </p>
                    </div>

                    <a
                        :href="route('instructor.gradebook.export', section.id)"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#e1e8fd] px-4 py-2 text-sm font-medium text-[#004ac6] hover:bg-[#dbe1ff] focus:outline-none focus:ring-2 focus:ring-[#004ac6] dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                        aria-label="Export gradebook as CSV"
                    >
                        Export CSV
                    </a>
                </header>

                <!-- Empty state: no students -->
                <div
                    v-if="students.length === 0"
                    role="status"
                    class="rounded-xl bg-[#f1f3ff] px-6 py-16 text-center dark:bg-slate-800"
                >
                    <p class="text-sm text-[#434655] dark:text-slate-400">
                        No enrolled students yet.
                    </p>
                </div>

                <!-- Empty state: no assignments -->
                <div
                    v-else-if="assignments.length === 0"
                    role="status"
                    class="rounded-xl bg-[#f1f3ff] px-6 py-16 text-center dark:bg-slate-800"
                >
                    <p class="text-sm text-[#434655]">No published assignments yet.</p>
                </div>

                <!-- Gradebook matrix -->
                <div v-else class="overflow-hidden rounded-xl bg-white dark:bg-slate-800">
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full border-collapse text-sm"
                            :aria-label="`Gradebook for ${section.course.code} ${section.section_name}`"
                        >
                            <!-- Column headers -->
                            <thead class="bg-[#f1f3ff] dark:bg-slate-900">
                                <tr>
                                    <th
                                        scope="col"
                                        class="sticky left-0 z-10 min-w-[180px] bg-[#f1f3ff] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#434655] dark:bg-slate-900 dark:text-slate-400"
                                    >
                                        Student
                                    </th>
                                    <th
                                        v-for="assignment in assignments"
                                        :key="assignment.id"
                                        scope="col"
                                        class="min-w-[110px] px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-[#434655] dark:text-slate-400"
                                    >
                                        <span class="block truncate" :title="assignment.title">
                                            {{ assignment.title }}
                                        </span>
                                        <span
                                            class="block font-normal normal-case text-[#737686] dark:text-slate-500"
                                        >
                                            /{{ assignment.max_score }}
                                        </span>
                                    </th>
                                    <th
                                        scope="col"
                                        class="min-w-[80px] px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-[#434655] dark:text-slate-400"
                                    >
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <!-- Student rows -->
                            <tbody class="bg-white dark:bg-slate-800">
                                <tr
                                    v-for="student in students"
                                    :key="student.id"
                                    class="transition-colors hover:bg-[#f1f3ff] dark:hover:bg-slate-700/50"
                                >
                                    <!-- Sticky student name -->
                                    <td
                                        class="sticky left-0 z-10 whitespace-nowrap bg-white px-6 py-3 font-medium text-[#141b2b] dark:bg-slate-800 dark:text-white"
                                    >
                                        {{ student.name }}
                                    </td>

                                    <!-- Grade cells -->
                                    <td
                                        v-for="assignment in assignments"
                                        :key="assignment.id"
                                        class="px-4 py-3 text-center"
                                    >
                                        <span
                                            v-if="student.grades[assignment.id]"
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            :class="
                                                cellClass(
                                                    student.grades[assignment.id],
                                                    assignment.max_score
                                                )
                                            "
                                        >
                                            {{ student.grades[assignment.id]!.score }}
                                            <CheckIcon
                                                v-if="student.grades[assignment.id]!.released"
                                                class="h-3 w-3"
                                                aria-label="Released"
                                            />
                                            <LockClosedIcon
                                                v-else
                                                class="h-3 w-3"
                                                aria-label="Not yet released"
                                            />
                                        </span>
                                        <span
                                            v-else
                                            class="text-[#737686] dark:text-slate-600"
                                            aria-label="Not submitted"
                                        >
                                            —
                                        </span>
                                    </td>

                                    <!-- Row total -->
                                    <td
                                        class="px-4 py-3 text-center font-semibold text-[#141b2b] dark:text-gray-300"
                                    >
                                        {{ student.total > 0 ? student.total : '—' }}
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Averages footer -->
                            <tfoot class="bg-[#f1f3ff] dark:bg-slate-900">
                                <tr>
                                    <td
                                        class="sticky left-0 z-10 bg-[#f1f3ff] px-6 py-3 text-xs font-semibold uppercase tracking-wider text-[#434655] dark:bg-slate-900 dark:text-slate-400"
                                    >
                                        Average
                                    </td>
                                    <td
                                        v-for="assignment in assignments"
                                        :key="assignment.id"
                                        class="px-4 py-3 text-center text-xs font-medium text-[#434655]"
                                    >
                                        {{
                                            averages[assignment.id] !== null &&
                                            averages[assignment.id] !== undefined
                                                ? averages[assignment.id]
                                                : '—'
                                        }}
                                    </td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Legend -->
                <div
                    v-if="students.length > 0 && assignments.length > 0"
                    class="mt-3 flex flex-wrap items-center gap-4 text-xs text-[#434655]"
                    aria-label="Score color legend"
                >
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-green-500"></span> ≥ 90%
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-[#004ac6]"></span> ≥ 75%
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span> ≥ 60%
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-red-500"></span> &lt; 60%
                    </span>
                    <span class="flex items-center gap-1">
                        <LockClosedIcon class="h-3 w-3" aria-hidden="true" /> Not released
                    </span>
                    <span class="flex items-center gap-1">
                        <CheckIcon class="h-3 w-3" aria-hidden="true" /> Released to student
                    </span>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
