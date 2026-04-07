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
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#737686]"
            aria-hidden="true"
        />
        <input
            :id="id"
            :value="modelValue"
            :type="type ?? 'text'"
            class="block w-full rounded-lg border border-transparent bg-[#f1f3ff] py-2.5 pl-10 pr-3 text-sm text-[#141b2b] placeholder-[#737686] transition focus:border-[#004ac6] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#004ac6] disabled:cursor-not-allowed disabled:opacity-60 aria-[invalid=true]:border-[#ba1a1a] aria-[invalid=true]:focus:ring-[#ba1a1a]"
            v-bind="$attrs"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        />
    </div>
</template>
