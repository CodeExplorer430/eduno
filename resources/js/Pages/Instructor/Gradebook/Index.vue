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
    if (!cell) return 'text-gray-300';
    const pct = scorePercent(cell.score, maxScore);
    if (pct >= 90) return 'text-green-700 bg-green-50';
    if (pct >= 75) return 'text-blue-700 bg-blue-50';
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
                        <h1 id="gradebook-heading" class="text-xl font-bold text-gray-900">
                            Gradebook
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            <span class="font-mono font-medium">{{ section.course.code }}</span>
                            — {{ section.course.title }} · Section {{ section.section_name }}
                        </p>
                    </div>

                    <a
                        :href="route('instructor.gradebook.export', section.id)"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Export gradebook as CSV"
                    >
                        Export CSV
                    </a>
                </header>

                <!-- Empty state: no students -->
                <div
                    v-if="students.length === 0"
                    role="status"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                >
                    <p class="text-sm text-gray-500">No enrolled students yet.</p>
                </div>

                <!-- Empty state: no assignments -->
                <div
                    v-else-if="assignments.length === 0"
                    role="status"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center"
                >
                    <p class="text-sm text-gray-500">No published assignments yet.</p>
                </div>

                <!-- Gradebook matrix -->
                <div
                    v-else
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full border-collapse text-sm"
                            :aria-label="`Gradebook for ${section.course.code} ${section.section_name}`"
                        >
                            <!-- Column headers -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="sticky left-0 z-10 min-w-[180px] bg-gray-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Student
                                    </th>
                                    <th
                                        v-for="assignment in assignments"
                                        :key="assignment.id"
                                        scope="col"
                                        class="min-w-[110px] px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        <span class="block truncate" :title="assignment.title">
                                            {{ assignment.title }}
                                        </span>
                                        <span class="block font-normal normal-case text-gray-400">
                                            /{{ assignment.max_score }}
                                        </span>
                                    </th>
                                    <th
                                        scope="col"
                                        class="min-w-[80px] px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <!-- Student rows -->
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="student in students"
                                    :key="student.id"
                                    class="transition-colors hover:bg-gray-50"
                                >
                                    <!-- Sticky student name -->
                                    <td
                                        class="sticky left-0 z-10 whitespace-nowrap bg-white px-6 py-3 font-medium text-gray-900 group-hover:bg-gray-50"
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
                                            class="text-gray-300"
                                            aria-label="Not submitted"
                                        >
                                            —
                                        </span>
                                    </td>

                                    <!-- Row total -->
                                    <td class="px-4 py-3 text-center font-semibold text-gray-700">
                                        {{ student.total > 0 ? student.total : '—' }}
                                    </td>
                                </tr>
                            </tbody>

                            <!-- Averages footer -->
                            <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                                <tr>
                                    <td
                                        class="sticky left-0 z-10 bg-gray-50 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500"
                                    >
                                        Average
                                    </td>
                                    <td
                                        v-for="assignment in assignments"
                                        :key="assignment.id"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-600"
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
                    class="mt-3 flex flex-wrap items-center gap-4 text-xs text-gray-500"
                    aria-label="Score color legend"
                >
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-green-500"></span> ≥ 90%
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="h-3 w-3 rounded-full bg-blue-500"></span> ≥ 75%
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
