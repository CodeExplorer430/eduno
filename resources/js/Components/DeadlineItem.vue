<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    assignment: {
        id: number;
        title: string;
        course_name: string;
        course_code?: string;
        due_at: string;
    };
}>();

const dueDate = computed(() => new Date(props.assignment.due_at));
const now = new Date();

const hoursUntilDue = computed(() =>
    Math.floor((dueDate.value.getTime() - now.getTime()) / (1000 * 60 * 60))
);

const urgencyClasses = computed(() => {
    if (hoursUntilDue.value <= 24) return 'border-red-400 bg-red-50 text-red-800';
    if (hoursUntilDue.value <= 72) return 'border-amber-400 bg-amber-50 text-amber-800';
    return 'border-green-400 bg-green-50 text-green-800';
});

const urgencyLabel = computed(() => {
    if (hoursUntilDue.value <= 24) return 'Due very soon';
    if (hoursUntilDue.value <= 72) return 'Due soon';
    return 'Upcoming';
});

const formattedDate = computed(() =>
    dueDate.value.toLocaleDateString('en-PH', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
);
</script>

<template>
    <li
        role="listitem"
        :aria-label="`${assignment.title} — ${assignment.course_name}, due ${formattedDate}. ${urgencyLabel}.`"
        class="flex items-start gap-3 rounded-lg border-l-4 p-4"
        :class="urgencyClasses"
    >
        <div class="min-w-0 flex-1">
            <p class="truncate font-medium text-sm">{{ assignment.title }}</p>
            <p class="text-xs mt-0.5 opacity-75">
                {{ assignment.course_code ? `${assignment.course_code} — ` : ''
                }}{{ assignment.course_name }}
            </p>
        </div>
        <time :datetime="assignment.due_at" class="shrink-0 text-xs font-medium whitespace-nowrap">
            {{ formattedDate }}
        </time>
    </li>
</template>
