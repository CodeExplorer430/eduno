<script setup lang="ts">
withDefaults(
    defineProps<{
        as?: string;
        headingId?: string;
    }>(),
    { as: 'section', headingId: undefined }
);
</script>

<template>
    <component
        :is="as"
        :aria-labelledby="headingId"
        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
    >
        <slot />
    </component>
</template>
