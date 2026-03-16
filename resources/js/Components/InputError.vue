<script setup lang="ts">
defineProps<{
    message?: string;
    id?: string;
}>();
</script>

<template>
    <div v-show="message" :id="id">
        <p class="text-sm text-red-600">
            {{ message }}
        </p>
    </div>
</template>
