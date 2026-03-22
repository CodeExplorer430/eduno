<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    title: string;
    description?: string;
    icon?: Component;
}>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white py-20 text-center"
        role="status"
    >
        <component :is="icon" v-if="icon" class="mb-4 h-12 w-12 text-gray-300" aria-hidden="true" />
        <p class="text-base font-medium text-gray-500">{{ title }}</p>
        <p v-if="description" class="mt-1 text-sm text-gray-400">{{ description }}</p>
        <slot />
    </div>
</template>
