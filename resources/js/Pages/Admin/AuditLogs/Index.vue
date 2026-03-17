<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from 'primevue/button';
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

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleString();
}
</script>

<template>
    <Head title="Audit Logs — Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900">Audit Logs</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <main>
                    <!-- Filter bar -->
                    <section
                        aria-label="Filter audit logs"
                        class="mb-6 flex flex-wrap items-end gap-4"
                    >
                        <div>
                            <label
                                for="filter_action"
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Action</label
                            >
                            <input
                                id="filter_action"
                                v-model="filterAction"
                                type="text"
                                placeholder="e.g. user.role_changed"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="filter_actor"
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Actor Email</label
                            >
                            <input
                                id="filter_actor"
                                v-model="filterActorEmail"
                                type="email"
                                placeholder="user@example.com"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="filter_from"
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >From</label
                            >
                            <input
                                id="filter_from"
                                v-model="filterFrom"
                                type="date"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="filter_to"
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >To</label
                            >
                            <input
                                id="filter_to"
                                v-model="filterTo"
                                type="date"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                        <Button
                            label="Apply"
                            severity="secondary"
                            size="small"
                            @click="applyFilters"
                        />
                    </section>

                    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200"
                                aria-label="Audit log entries"
                            >
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Timestamp
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Actor
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Action
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Entity Type
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Entity ID
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Metadata
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <tr v-if="logs.data.length === 0">
                                        <td
                                            colspan="6"
                                            class="px-6 py-10 text-center text-sm text-gray-500"
                                        >
                                            No audit log entries found.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="log in logs.data"
                                        :key="log.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td
                                            class="whitespace-nowrap px-4 py-3 text-sm text-gray-500"
                                        >
                                            {{ formatDate(log.created_at) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <span v-if="log.actor">
                                                <span class="font-medium">{{
                                                    log.actor.name
                                                }}</span>
                                                <span class="block text-xs text-gray-500">{{
                                                    log.actor.email
                                                }}</span>
                                            </span>
                                            <span v-else class="text-gray-400">System</span>
                                        </td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-900">
                                            {{ log.action }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ log.entity_type }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ log.entity_id ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <details v-if="log.metadata">
                                                <summary
                                                    class="cursor-pointer text-indigo-600 hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                >
                                                    View
                                                </summary>
                                                <pre
                                                    class="mt-2 max-w-xs overflow-auto rounded bg-gray-50 p-2 text-xs"
                                                    >{{
                                                        JSON.stringify(log.metadata, null, 2)
                                                    }}</pre
                                                >
                                            </details>
                                            <span v-else>—</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav
                        v-if="logs.links && logs.links.length > 3"
                        aria-label="Pagination"
                        class="mt-6 flex justify-center gap-1"
                    >
                        <template v-for="link in logs.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :aria-current="link.active ? 'page' : undefined"
                                class="rounded-md px-3 py-2 text-sm"
                                :class="
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-50'
                                "
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="rounded-md px-3 py-2 text-sm text-gray-400"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
