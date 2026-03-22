<script setup lang="ts">
withDefaults(defineProps<{ type?: 'button' | 'submit' | 'reset' }>(), { type: 'button' });
</script>

<template>
    <button
        v-ripple
        :type="type"
        class="inline-flex items-center gap-2 rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 overflow-hidden relative"
    >
        <slot name="icon" />
        <slot />
    </button>
</template>
