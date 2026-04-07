<script setup lang="ts">
withDefaults(defineProps<{ type?: 'button' | 'submit' | 'reset' }>(), { type: 'submit' });
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center gap-2 rounded-[8px] bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-[#acedff] focus:ring-offset-2 active:brightness-90 disabled:opacity-60 disabled:cursor-not-allowed"
    >
        <slot name="icon" />
        <slot />
    </button>
</template>
