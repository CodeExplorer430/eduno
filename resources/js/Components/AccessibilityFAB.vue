<script setup lang="ts">
import { Cog6ToothIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link
        :href="route('profile.accessibility.edit')"
        class="fixed bottom-20 right-4 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-[#f9f9ff] shadow-[0_20px_40px_rgba(20,27,43,0.06)] transition-shadow hover:shadow-[0_20px_40px_rgba(20,27,43,0.12)] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-2 lg:bottom-6"
        aria-label="Accessibility settings"
    >
        <Cog6ToothIcon class="h-6 w-6 text-[#ffddb8]" aria-hidden="true" />
    </Link>
</template>
