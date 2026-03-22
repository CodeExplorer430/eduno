<script setup lang="ts">
import { computed } from 'vue';
import type { Component } from 'vue';

const props = withDefaults(
    defineProps<{
        label: string;
        icon?: Component;
        valueClass?: string;
        animationDelay?: number;
        accent?: 'blue' | 'cyan' | 'amber' | 'green' | 'red';
    }>(),
    { valueClass: 'text-gray-900', animationDelay: 0, accent: 'blue' }
);

const accentClasses = computed(() => {
    const map: Record<string, { bg: string; icon: string }> = {
        blue: { bg: 'bg-blue-50', icon: 'text-blue-600' },
        cyan: { bg: 'bg-cyan-50', icon: 'text-cyan-500' },
        amber: { bg: 'bg-amber-50', icon: 'text-amber-500' },
        green: { bg: 'bg-green-50', icon: 'text-green-600' },
        red: { bg: 'bg-red-50', icon: 'text-red-500' },
    };
    return map[props.accent] ?? map['blue'];
});
</script>

<template>
    <div
        v-animateonscroll="{ enterClass: 'animate-fadein' }"
        :style="`animation-delay: ${animationDelay}ms`"
        class="overflow-hidden rounded-lg bg-white px-6 py-5 shadow-sm"
    >
        <div
            v-if="icon"
            class="mb-3 flex h-10 w-10 items-center justify-center rounded-full"
            :class="accentClasses.bg"
        >
            <component :is="icon" class="h-5 w-5" :class="accentClasses.icon" aria-hidden="true" />
        </div>
        <dt class="text-sm font-medium text-gray-500">{{ label }}</dt>
        <dd class="mt-1 text-3xl font-semibold" :class="valueClass">
            <slot />
        </dd>
    </div>
</template>
