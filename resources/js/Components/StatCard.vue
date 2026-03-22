<script setup lang="ts">
import type { Component } from 'vue';

withDefaults(
    defineProps<{
        label: string;
        icon?: Component;
        valueClass?: string;
        animationDelay?: number;
    }>(),
    { valueClass: 'text-gray-900', animationDelay: 0 }
);
</script>

<template>
    <div
        v-animateonscroll="{ enterClass: 'animate-fadein' }"
        :style="`animation-delay: ${animationDelay}ms`"
        class="overflow-hidden rounded-lg bg-white px-6 py-5 shadow-sm"
    >
        <component :is="icon" v-if="icon" class="mb-2 h-5 w-5 text-gray-400" aria-hidden="true" />
        <dt class="text-sm font-medium text-gray-500">{{ label }}</dt>
        <dd class="mt-1 text-3xl font-semibold" :class="valueClass">
            <slot />
        </dd>
    </div>
</template>
