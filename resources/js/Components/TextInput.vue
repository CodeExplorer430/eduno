<script setup lang="ts">
import { onMounted, ref } from 'vue';

defineOptions({ inheritAttrs: false });

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        ref="input"
        v-bind="$attrs"
        v-model="model"
        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
    />
</template>
