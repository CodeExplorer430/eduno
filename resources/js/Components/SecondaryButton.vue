<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    }
);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center gap-2 rounded-[8px] bg-[#e1e8fd] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-[#004e5c] transition duration-150 ease-in-out hover:brightness-95 focus:outline-none focus:ring-2 focus:ring-[#acedff] focus:ring-offset-2 disabled:opacity-25"
    >
        <slot name="icon" />
        <slot />
    </button>
</template>
