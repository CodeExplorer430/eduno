<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm } from '@inertiajs/vue3';
import type { AppNotification, PaginatedResponse } from '@/Types/models';

defineProps<{
    notifications: PaginatedResponse<AppNotification>;
    unread_count: number;
}>();

function formatDate(iso: string): string {
    return new Date(iso).toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function markRead(notificationId: string): void {
    useForm({}).post(route('notifications.mark-as-read', notificationId));
}

const markAllForm = useForm({});
function markAll(): void {
    markAllForm.post(route('notifications.read-all'));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Notifications</h1>
                <button
                    v-if="unread_count > 0"
                    type="button"
                    :disabled="markAllForm.processing"
                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    @click="markAll"
                >
                    Mark all as read
                </button>
            </div>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <p
                v-if="notifications.data.length === 0"
                role="status"
                class="text-center text-gray-500"
            >
                You have no notifications yet.
            </p>

            <ul v-else aria-label="Notifications list" class="space-y-2">
                <li
                    v-for="n in notifications.data"
                    :key="n.id"
                    :class="[
                        'flex items-start gap-3 rounded-lg border p-4 transition-colors',
                        n.read_at === null
                            ? 'border-indigo-200 bg-indigo-50'
                            : 'border-gray-200 bg-white',
                    ]"
                >
                    <!-- read/unread indicator dot -->
                    <span
                        :aria-label="n.read_at === null ? 'Unread' : 'Read'"
                        :class="[
                            'mt-1 h-2.5 w-2.5 shrink-0 rounded-full',
                            n.read_at === null
                                ? 'bg-indigo-500'
                                : 'border border-gray-400 bg-transparent',
                        ]"
                    />

                    <div class="min-w-0 flex-1">
                        <a
                            :href="route('notifications.show', n.id)"
                            class="block text-sm font-medium text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                            >{{ n.data.message }}</a
                        >
                        <p class="mt-0.5 text-xs text-gray-500">{{ formatDate(n.created_at) }}</p>
                    </div>

                    <button
                        v-if="n.read_at === null"
                        type="button"
                        class="shrink-0 rounded text-xs text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                        :aria-label="`Mark notification as read: ${n.data.message}`"
                        @click="markRead(n.id)"
                    >
                        Mark as read
                    </button>
                </li>
            </ul>

            <Pagination v-if="notifications.links.length > 3" :links="notifications.links" />
        </div>
    </AuthenticatedLayout>
</template>
