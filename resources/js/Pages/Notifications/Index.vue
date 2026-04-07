<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
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

type NotificationGroup = { label: string; items: AppNotification[] };

function getGroupLabel(iso: string): string {
    const d = new Date(iso);
    const now = new Date();
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const weekStart = new Date(todayStart);
    weekStart.setDate(weekStart.getDate() - 6);
    if (d >= todayStart) return 'Today';
    if (d >= weekStart) return 'This Week';
    return 'Earlier';
}

function useGroupedNotifications(items: AppNotification[]): NotificationGroup[] {
    const order = ['Today', 'This Week', 'Earlier'];
    const map: Record<string, AppNotification[]> = {};
    for (const n of items) {
        const label = getGroupLabel(n.created_at);
        (map[label] ??= []).push(n);
    }
    return order.filter((l) => map[l]).map((label) => ({ label, items: map[label] }));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-[#141b2b] dark:text-white">Notifications</h1>
                <button
                    v-if="unread_count > 0"
                    type="button"
                    :disabled="markAllForm.processing"
                    class="text-sm font-medium text-[#004ac6] hover:underline focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-2 disabled:opacity-50 rounded"
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
                class="text-center text-[#434655] dark:text-slate-400"
            >
                You have no notifications yet.
            </p>

            <template v-else>
                <template
                    v-for="group in useGroupedNotifications(notifications.data)"
                    :key="group.label"
                >
                    <p
                        class="mt-6 first:mt-0 mb-2 px-1 text-xs font-semibold uppercase tracking-wider text-[#434655] dark:text-slate-400"
                    >
                        {{ group.label }}
                    </p>

                    <ul :aria-label="`${group.label} notifications`" class="space-y-2">
                        <li
                            v-for="n in group.items"
                            :key="n.id"
                            :class="[
                                'flex items-start gap-3 rounded-xl overflow-hidden',
                                n.read_at === null
                                    ? 'bg-white border-l-4 border-[#004ac6] pl-3 pr-4 py-4'
                                    : 'bg-[#f1f3ff] dark:bg-slate-800 px-4 py-4',
                            ]"
                        >
                            <!-- read/unread indicator dot -->
                            <span
                                :aria-label="n.read_at === null ? 'Unread' : 'Read'"
                                :class="[
                                    'mt-1 h-2.5 w-2.5 shrink-0 rounded-full',
                                    n.read_at === null
                                        ? 'bg-[#004ac6]'
                                        : 'border border-[#c3c6d7] bg-transparent',
                                ]"
                            />

                            <div class="min-w-0 flex-1">
                                <a
                                    :href="route('notifications.show', n.id)"
                                    class="block text-sm font-medium text-[#141b2b] dark:text-white hover:underline focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1 rounded"
                                    >{{ n.data.message }}</a
                                >
                                <p class="mt-0.5 text-xs text-[#737686] dark:text-slate-400">
                                    {{ formatDate(n.created_at) }}
                                </p>
                            </div>

                            <button
                                v-if="n.read_at === null"
                                type="button"
                                class="shrink-0 rounded text-xs text-[#004ac6] hover:text-[#00174b] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1"
                                :aria-label="`Mark notification as read: ${n.data.message}`"
                                @click="markRead(n.id)"
                            >
                                Mark as read
                            </button>
                        </li>
                    </ul>
                </template>
            </template>

            <Pagination v-if="notifications.links.length > 3" :links="notifications.links" />
        </div>
    </AuthenticatedLayout>
</template>
