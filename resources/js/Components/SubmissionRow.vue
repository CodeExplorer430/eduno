<script setup lang="ts">
import Tag from 'primevue/tag';
import { Link } from '@inertiajs/vue3';

interface Props {
    submission: {
        id: number;
        student: { id: number; name: string };
        submitted_at: string;
        is_late: boolean;
        attempt_no: number;
        status: string;
        grade?: { score: number; released_at: string | null } | null;
    };
    maxScore: number;
}

const props = defineProps<Props>();

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateString));

const statusSeverity: Record<string, 'info' | 'success' | 'secondary' | 'danger' | 'warn'> = {
    submitted: 'info',
    graded: 'success',
    returned: 'secondary',
    late: 'danger',
    pending: 'warn',
};

const scoreLabel = (): string => {
    if (!props.submission.grade) return '—';
    return `${props.submission.grade.score} / ${props.maxScore}`;
};
</script>

<template>
    <tr class="transition hover:bg-gray-50">
        <td class="px-6 py-4 text-sm font-medium text-gray-900">
            {{ submission.student.name }}
        </td>
        <td class="px-6 py-4 text-sm text-gray-600">
            <time :datetime="submission.submitted_at">
                {{ formatDate(submission.submitted_at) }}
            </time>
        </td>
        <td class="px-6 py-4 text-sm">
            <span
                v-if="submission.is_late"
                class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
            >
                Late
            </span>
            <span v-else class="text-gray-400">On time</span>
        </td>
        <td class="px-6 py-4 text-sm text-gray-600">
            {{ submission.attempt_no }}
        </td>
        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
            <span
                :aria-label="
                    submission.grade
                        ? `Score: ${submission.grade.score} out of ${maxScore}`
                        : 'Not yet graded'
                "
            >
                {{ scoreLabel() }}
            </span>
        </td>
        <td class="px-6 py-4 text-sm">
            <Tag
                :severity="statusSeverity[submission.status] ?? 'warn'"
                :value="submission.status"
                class="capitalize"
            />
        </td>
        <td class="px-6 py-4 text-sm">
            <Link
                :href="route('instructor.submissions.show', submission.id)"
                class="font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                :aria-label="`View submission by ${submission.student.name}`"
            >
                View
            </Link>
        </td>
    </tr>
</template>
