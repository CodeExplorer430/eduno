<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useFormatDate } from '@/composables/useFormatDate';

interface Assignment {
    id: number;
    title: string;
    due_at: string | null;
    max_score: number;
    course_section_id: number;
}

const props = defineProps<{
    assignment: Assignment;
}>();

const { formatDate } = useFormatDate();

const urgency = computed<'past' | 'soon' | 'normal'>(() => {
    if (!props.assignment.due_at) return 'normal';
    const due = new Date(props.assignment.due_at);
    const now = new Date();
    if (due < now) return 'past';
    const hoursLeft = (due.getTime() - now.getTime()) / 3600000;
    if (hoursLeft < 24) return 'soon';
    return 'normal';
});

const dueDateClass = computed<string>(() => {
    if (urgency.value === 'past') return 'text-red-600 font-medium';
    if (urgency.value === 'soon') return 'text-amber-600 font-medium';
    return 'text-gray-600';
});

const urgencyBadge = computed<string | null>(() => {
    if (urgency.value === 'past') return 'Past Due';
    if (urgency.value === 'soon') return 'Due Soon';
    return null;
});

const urgencyBadgeClass = computed<string>(() => {
    if (urgency.value === 'past') return 'bg-red-100 text-red-700';
    if (urgency.value === 'soon') return 'bg-amber-100 text-amber-700';
    return '';
});
</script>

<template>
    <article
        class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:shadow-md"
    >
        <div class="p-5">
            <header class="mb-3 flex items-start justify-between gap-2">
                <h2 class="text-base font-semibold text-gray-900 leading-snug">
                    {{ assignment.title }}
                </h2>
                <span
                    v-if="urgencyBadge"
                    class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="urgencyBadgeClass"
                    aria-label="Urgency status"
                >
                    {{ urgencyBadge }}
                </span>
            </header>

            <dl class="space-y-1.5 text-sm">
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-gray-500">Due</dt>
                    <dd
                        :class="dueDateClass"
                        :aria-label="
                            urgency === 'past'
                                ? `Past due: ${formatDate(assignment.due_at)}`
                                : `Due: ${formatDate(assignment.due_at)}`
                        "
                    >
                        <time :datetime="assignment.due_at ?? ''">{{
                            formatDate(assignment.due_at)
                        }}</time>
                    </dd>
                </div>
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-gray-500">Max Score</dt>
                    <dd class="text-gray-600">{{ assignment.max_score }} pts</dd>
                </div>
            </dl>
        </div>

        <footer class="border-t border-gray-100 px-5 py-3">
            <Link
                :href="route('student.assignments.show', assignment.id)"
                class="inline-flex items-center gap-1 rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                :aria-label="`View assignment: ${assignment.title}`"
            >
                View Assignment
                <svg
                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </Link>
        </footer>
    </article>
</template>
