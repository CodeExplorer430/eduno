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
        class="overflow-hidden rounded-xl bg-white dark:bg-slate-800"
    >
        <slot />
    </component>
</template>
