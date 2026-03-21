<script setup lang="ts">
defineProps<{
    message?: string;
    id?: string;
}>();
</script>

<template>
    <div v-show="message" :id="id" role="alert">
        <p class="text-sm text-red-600" aria-live="assertive">
            {{ message }}
        </p>
    </div>
</template>
