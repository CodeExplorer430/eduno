<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { UserGroupIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Summary {
    total: number;
    student: number;
    instructor: number;
    admin: number;
}

interface Props {
    users: {
        data: User[];
        links: PaginationLink[];
    };
    filters: { search?: string | null; role?: string | null };
    summary: Summary;
    roles: { name: string; value: string }[];
}

const props = defineProps<Props>();

const search = ref<string>(props.filters.search ?? '');
const roleFilter = ref<string>(props.filters.role ?? '');

let searchTimer: ReturnType<typeof setTimeout> | null = null;

function applyFilters(): void {
    router.get(
        route('admin.users.index'),
        {
            search: search.value || undefined,
            role: roleFilter.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

watch(search, () => {
    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 350);
});

watch(roleFilter, applyFilters);

const successMessage = computed(
    () => (usePage().props.flash as Record<string, string> | undefined)?.success ?? null
);

const showFlash = ref<boolean>(true);
watch(successMessage, () => {
    showFlash.value = true;
});

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));

const roleClasses: Record<string, string> = {
    admin: 'bg-purple-100 text-purple-700',
    instructor: 'bg-blue-100 text-blue-700',
    student: 'bg-green-100 text-green-700',
};

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
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="flex items-center gap-2 text-xl font-bold text-gray-900 dark:text-white">
                <UserGroupIcon
                    class="h-6 w-6 text-gray-700 dark:text-gray-300"
                    aria-hidden="true"
                />
                User Management
            </h1>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Stats strip -->
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Total Users
                    </p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ summary.total }}
                    </p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-600">
                        Students
                    </p>
                    <p class="mt-1 text-2xl font-bold text-green-700">{{ summary.student }}</p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                        Instructors
                    </p>
                    <p class="mt-1 text-2xl font-bold text-blue-700">{{ summary.instructor }}</p>
                </div>
                <div
                    class="overflow-hidden rounded-xl bg-white px-5 py-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-600">
                        Admins
                    </p>
                    <p class="mt-1 text-2xl font-bold text-purple-700">{{ summary.admin }}</p>
                </div>
            </div>

            <!-- Flash message -->
            <div
                v-if="successMessage && showFlash"
                role="status"
                aria-live="polite"
                class="mb-4 flex items-center justify-between rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200"
            >
                <span>{{ successMessage }}</span>
                <button
                    type="button"
                    class="ml-4 rounded text-green-500 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    aria-label="Dismiss"
                    @click="showFlash = false"
                >
                    &times;
                </button>
            </div>

            <!-- Filter bar -->
            <section
                aria-label="Filter users"
                class="mb-6 flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div class="flex-1 min-w-48">
                    <label
                        for="user-search"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Search
                    </label>
                    <input
                        id="user-search"
                        v-model="search"
                        type="search"
                        placeholder="Name or email…"
                        class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                        aria-label="Search users by name or email"
                    />
                </div>
                <div>
                    <label
                        for="role-filter"
                        class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                    >
                        Role
                    </label>
                    <select
                        id="role-filter"
                        v-model="roleFilter"
                        class="block w-40 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="">All Roles</option>
                        <option v-for="r in roles" :key="r.value" :value="r.value">
                            {{ r.name }}
                        </option>
                    </select>
                </div>
            </section>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div
                    class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                >
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">All Users</h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-100 dark:divide-slate-700"
                        aria-label="Registered users"
                    >
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    User
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Role
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    Joined
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400"
                                >
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 bg-white dark:divide-slate-700 dark:bg-slate-800"
                        >
                            <tr v-if="users.data.length === 0">
                                <td
                                    colspan="4"
                                    class="px-6 py-12 text-center text-sm text-gray-400 dark:text-slate-500"
                                >
                                    No users found.
                                </td>
                            </tr>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="transition-colors hover:bg-gray-50 dark:hover:bg-slate-700/50"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                            aria-hidden="true"
                                        >
                                            {{ getInitials(user.name) }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ user.name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                        :class="
                                            roleClasses[user.role] ?? 'bg-gray-100 text-gray-600'
                                        "
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                                    <time :datetime="user.created_at">
                                        {{ formatDate(user.created_at) }}
                                    </time>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <Link
                                        :href="route('admin.users.edit', user.id)"
                                        class="inline-flex items-center gap-1 rounded font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-label="`Edit ${user.name}`"
                                    >
                                        <PencilSquareIcon class="h-4 w-4" aria-hidden="true" />
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination v-if="users.links.length > 3" :links="users.links" class="mt-4" />
        </div>
    </AuthenticatedLayout>
</template>
