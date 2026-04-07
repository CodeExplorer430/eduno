<script setup lang="ts">
defineProps<{
    value?: string;
    for?: string;
}>();
</script>

<template>
    <label :for="$props.for" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
