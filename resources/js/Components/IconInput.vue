<script setup lang="ts">
import type { Component } from 'vue';

defineOptions({ inheritAttrs: false });

defineProps<{
    id: string;
    icon: Component;
    modelValue: string;
    type?: string;
}>();

defineEmits<{ 'update:modelValue': [v: string] }>();
</script>

<template>
    <div class="relative mt-1">
        <component
            :is="icon"
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
            aria-hidden="true"
        />
        <input
            :id="id"
            :value="modelValue"
            :type="type ?? 'text'"
            class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 aria-[invalid=true]:border-red-500 aria-[invalid=true]:focus:ring-red-500"
            v-bind="$attrs"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        />
    </div>
</template>
