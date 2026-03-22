<script setup lang="ts">
import type { Component } from 'vue';
import InputText from 'primevue/inputtext';

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
        <InputText
            :id="id"
            :modelValue="modelValue"
            :type="type ?? 'text'"
            class="block w-full pl-9"
            v-bind="$attrs"
            @update:modelValue="$emit('update:modelValue', $event as string)"
        />
    </div>
</template>
