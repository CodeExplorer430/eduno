<script setup lang="ts">
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssignmentCard from '@/Components/AssignmentCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    course_section_id: number;
}

const props = defineProps<{
    assignments: Assignment[];
}>();

type Group = 'today' | 'week' | 'later' | 'past';

function getGroup(assignment: Assignment): Group {
    if (!assignment.due_at) return 'later';
    const due = new Date(assignment.due_at);
    const now = new Date();
    if (due < now) return 'past';
    const diffDays = (due.getTime() - now.getTime()) / 86400000;
    if (diffDays < 1) return 'today';
    if (diffDays < 7) return 'week';
    return 'later';
}

const grouped = computed<Record<Group, Assignment[]>>(() => {
    const result: Record<Group, Assignment[]> = { today: [], week: [], later: [], past: [] };
    for (const a of props.assignments) {
        result[getGroup(a)].push(a);
    }
    return result;
});

const groupConfig: { key: Group; label: string; accent: string }[] = [
    { key: 'today', label: 'Due Today', accent: 'text-red-600 border-red-200' },
    { key: 'week', label: 'Due This Week', accent: 'text-amber-600 border-amber-200' },
    { key: 'later', label: 'Later', accent: 'text-gray-600 border-gray-200' },
    { key: 'past', label: 'Past Due', accent: 'text-red-400 border-red-100' },
];
</script>

<template>
    <Head title="My Assignments" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800">My Assignments</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <EmptyState
                v-if="assignments.length === 0"
                :icon="ClipboardDocumentListIcon"
                title="No assignments found."
                description="Your instructors have not published any assignments yet."
            />

            <div v-else class="space-y-8">
                <section
                    v-for="group in groupConfig"
                    v-show="grouped[group.key].length > 0"
                    :key="group.key"
                    :aria-label="group.label"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 border-l-4 pl-3 text-sm font-semibold uppercase tracking-wider"
                        :class="group.accent"
                    >
                        {{ group.label }}
                        <span
                            class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-normal text-gray-600"
                        >
                            {{ grouped[group.key].length }}
                        </span>
                    </h2>
                    <div
                        class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3"
                        role="list"
                        :aria-label="`${group.label} assignments`"
                    >
                        <div
                            v-for="assignment in grouped[group.key]"
                            :key="assignment.id"
                            role="listitem"
                        >
                            <AssignmentCard :assignment="assignment" />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
