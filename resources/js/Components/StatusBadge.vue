<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        variant: 'draft' | 'late' | 'published' | 'submitted' | 'graded' | 'returned' | 'pending';
        label?: string;
    }>(),
    {
        label: undefined,
    }
);

const variantClasses: Record<string, string> = {
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    late: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    published: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    graded: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    returned: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    pending: 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300',
};

const displayLabel = props.label ?? props.variant.charAt(0).toUpperCase() + props.variant.slice(1);
</script>

<template>
    <span
        role="status"
        class="rounded-full px-2 py-0.5 text-xs font-medium"
        :class="variantClasses[variant]"
    >
        {{ displayLabel }}
    </span>
</template>
