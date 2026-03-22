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
    draft: 'bg-yellow-100 text-yellow-800',
    late: 'bg-red-100 text-red-700',
    published: 'bg-green-100 text-green-800',
    submitted: 'bg-blue-100 text-blue-800',
    graded: 'bg-green-100 text-green-800',
    returned: 'bg-blue-100 text-blue-800',
    pending: 'bg-gray-100 text-gray-600',
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
