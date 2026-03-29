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
    { valueClass: 'text-gray-900 dark:text-white', animationDelay: 0, accent: 'blue' }
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

const accentBarClass = computed(() => {
    const bars: Record<string, string> = {
        blue: 'bg-blue-500',
        cyan: 'bg-cyan-500',
        amber: 'bg-amber-500',
        green: 'bg-green-500',
        red: 'bg-red-500',
    };
    return bars[props.accent] ?? 'bg-blue-500';
});
</script>

<template>
    <div
        v-animateonscroll="{ enterClass: 'animate-fadein' }"
        :style="`animation-delay: ${animationDelay}ms`"
        class="relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-800 dark:ring-slate-700"
    >
        <!-- Left accent bar -->
        <div class="absolute left-0 top-0 h-full w-1 rounded-l-xl" :class="accentBarClass" />

        <div class="px-5 py-5 pl-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-slate-400"
                    >
                        {{ label }}
                    </p>
                    <p class="mt-2 text-3xl font-bold tracking-tight" :class="valueClass">
                        <slot />
                    </p>
                </div>
                <div
                    v-if="icon"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg"
                    :class="accentClasses.bg"
                >
                    <component
                        :is="icon"
                        class="h-5 w-5"
                        :class="accentClasses.icon"
                        aria-hidden="true"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
