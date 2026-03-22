<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { AuditLog } from '@/Types/models';

interface AuditLogWithActor extends AuditLog {
    actor?: { id: number; name: string; email: string } | null;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    logs: Paginated<AuditLogWithActor>;
    filters: {
        action?: string;
        actor_email?: string;
        from?: string;
        to?: string;
    };
}

const props = defineProps<Props>();

const filterAction = ref<string>(props.filters.action ?? '');
const filterActorEmail = ref<string>(props.filters.actor_email ?? '');
const filterFrom = ref<string>(props.filters.from ?? '');
const filterTo = ref<string>(props.filters.to ?? '');

function applyFilters(): void {
    router.get(
        route('admin.audit-logs.index'),
        {
            action: filterAction.value || undefined,
            actor_email: filterActorEmail.value || undefined,
            from: filterFrom.value || undefined,
            to: filterTo.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function formatRelative(dateStr: string): string {
    const diff = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'just now';
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    const days = Math.floor(hrs / 24);
    return `${days}d ago`;
}

function formatAbsolute(dateStr: string): string {
    return new Date(dateStr).toLocaleString('en-PH');
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase();
}
</script>

<template>
    <Head title="Audit Logs — Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900">Audit Logs</h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Filter bar -->
            <section
                aria-label="Filter audit logs"
                class="mb-6 flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100"
            >
                <div>
                    <label
                        for="filter_action"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >Action</label
                    >
                    <input
                        id="filter_action"
                        v-model="filterAction"
                        type="text"
                        placeholder="e.g. user.role_changed"
                        class="block rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label
                        for="filter_actor"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >Actor Email</label
                    >
                    <input
                        id="filter_actor"
                        v-model="filterActorEmail"
                        type="email"
                        placeholder="user@example.com"
                        class="block rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label
                        for="filter_from"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >From</label
                    >
                    <input
                        id="filter_from"
                        v-model="filterFrom"
                        type="date"
                        class="block rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label
                        for="filter_to"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500"
                        >To</label
                    >
                    <input
                        id="filter_to"
                        v-model="filterTo"
                        type="date"
                        class="block rounded-lg border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <button
                    type="button"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @click="applyFilters"
                >
                    Apply Filters
                </button>
            </section>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900">Log Entries</h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100"
                        aria-label="Audit log entries"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Timestamp
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Actor
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Action
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Entity
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Metadata
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-if="logs.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-12 text-center text-sm text-gray-400"
                                >
                                    No audit log entries found.
                                </td>
                            </tr>
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="transition-colors hover:bg-gray-50"
                            >
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                    <time
                                        :datetime="log.created_at"
                                        :title="formatAbsolute(log.created_at)"
                                    >
                                        {{ formatRelative(log.created_at) }}
                                    </time>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div v-if="log.actor" class="flex items-center gap-2">
                                        <div
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-600"
                                            aria-hidden="true"
                                        >
                                            {{ getInitials(log.actor.name) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ log.actor.name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ log.actor.email }}
                                            </p>
                                        </div>
                                    </div>
                                    <span v-else class="text-gray-400 italic">System</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-block rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-700"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    <p>{{ log.entity_type }}</p>
                                    <p v-if="log.entity_id" class="text-gray-400">
                                        ID: {{ log.entity_id }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <details v-if="log.metadata">
                                        <summary
                                            class="cursor-pointer text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                                        >
                                            View
                                        </summary>
                                        <pre
                                            class="mt-2 max-w-xs overflow-auto rounded-lg bg-gray-900 p-3 text-xs text-green-400"
                                            >{{ JSON.stringify(log.metadata, null, 2) }}</pre
                                        >
                                    </details>
                                    <span v-else class="text-gray-300">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination
                v-if="logs.links && logs.links.length > 3"
                :links="logs.links"
                class="mt-4"
            />
        </div>
    </AuthenticatedLayout>
</template>
