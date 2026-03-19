<script setup lang="ts">
defineProps<{ message: string }>();
</script>

<template>
    <div
        class="rounded border border-dashed border-gray-300 px-6 py-8 text-center text-gray-500"
        role="status"
    >
        {{ message }}
    </div>
</template>
