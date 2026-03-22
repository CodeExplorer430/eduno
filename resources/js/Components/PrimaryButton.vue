<script setup lang="ts">
withDefaults(defineProps<{ type?: 'button' | 'submit' | 'reset' }>(), { type: 'submit' });
</script>

<template>
    <button
        v-ripple
        :type="type"
        class="inline-flex items-center gap-2 rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-800 overflow-hidden relative disabled:opacity-60 disabled:cursor-not-allowed"
    >
        <slot name="icon" />
        <slot />
    </button>
</template>
