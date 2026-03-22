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

const isPastDue = computed<boolean>(() => {
    if (!props.assignment.due_at) return false;
    return new Date(props.assignment.due_at) < new Date();
});
</script>

<template>
    <article
        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
    >
        <div class="p-5">
            <header class="mb-3">
                <h2 class="text-base font-semibold text-gray-900">
                    {{ assignment.title }}
                </h2>
            </header>

            <dl class="space-y-1 text-sm">
                <div class="flex items-center gap-1">
                    <dt class="font-medium text-gray-700">Due:</dt>
                    <dd
                        :class="isPastDue ? 'text-red-600 font-medium' : 'text-gray-600'"
                        :aria-label="
                            isPastDue
                                ? `Past due: ${formatDate(assignment.due_at)}`
                                : `Due: ${formatDate(assignment.due_at)}`
                        "
                    >
                        <time :datetime="assignment.due_at ?? ''">{{
                            formatDate(assignment.due_at)
                        }}</time>
                    </dd>
                </div>
                <div class="flex items-center gap-1">
                    <dt class="font-medium text-gray-700">Max Score:</dt>
                    <dd class="text-gray-600">{{ assignment.max_score }}</dd>
                </div>
            </dl>
        </div>

        <footer class="border-t border-gray-100 bg-gray-50 px-5 py-3">
            <Link
                :href="route('student.assignments.show', assignment.id)"
                class="text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                :aria-label="`View assignment: ${assignment.title}`"
            >
                View Assignment &rarr;
            </Link>
        </footer>
    </article>
</template>
