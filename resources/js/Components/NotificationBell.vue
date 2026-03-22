<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import { BellIcon } from '@heroicons/vue/24/outline';

const page = usePage<PageProps>();
const count = computed(() => page.props.auth.unread_notifications_count ?? 0);
const label = computed(() =>
    count.value > 0 ? `Notifications (${count.value} unread)` : 'Notifications'
);
</script>

<template>
    <Link
        :href="route('notifications.index')"
        :aria-label="label"
        class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        <BellIcon class="h-6 w-6" aria-hidden="true" />

        <span
            v-if="count > 0"
            aria-hidden="true"
            class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
            >{{ count > 9 ? '9+' : count }}</span
        >
    </Link>
</template>
