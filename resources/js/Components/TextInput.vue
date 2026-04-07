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
        class="rounded-md border border-transparent bg-[#f1f3ff] text-[#141b2b] placeholder-[#737686] transition focus:border-[#004ac6] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
    />
</template>
