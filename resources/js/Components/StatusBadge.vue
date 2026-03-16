<script setup lang="ts">
defineProps<{
    status: 'submitted' | 'graded' | 'returned' | 'late' | 'pending';
}>();

const classes: Record<string, string> = {
    submitted: 'bg-blue-100 text-blue-800',
    graded: 'bg-green-100 text-green-800',
    returned: 'bg-purple-100 text-purple-800',
    late: 'bg-red-100 text-red-800',
    pending: 'bg-gray-100 text-gray-600',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
        :class="classes[status]"
    >
        {{ status }}
    </span>
</template>
