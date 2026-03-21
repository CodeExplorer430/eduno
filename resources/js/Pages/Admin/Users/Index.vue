<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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

interface Props {
    users: {
        data: User[];
        links: PaginationLink[];
    };
}

defineProps<Props>();

const decodeLabel = (label: string): string =>
    label
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
        .replace(/&amp;/g, '&');

const formatDate = (dateString: string): string =>
    new Intl.DateTimeFormat('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateString));

const roleClasses: Record<string, string> = {
    admin: 'bg-purple-100 text-purple-800',
    instructor: 'bg-blue-100 text-blue-800',
    student: 'bg-green-100 text-green-800',
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900">User Management</h1>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <main>
                    <section aria-labelledby="users-heading">
                        <h2 id="users-heading" class="sr-only">Users list</h2>

                        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                    aria-label="Registered users"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Name
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Email
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Role
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Joined
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <tr
                                            v-for="user in users.data"
                                            :key="user.id"
                                            class="transition hover:bg-gray-50"
                                        >
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ user.name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ user.email }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                    :class="
                                                        roleClasses[user.role] ??
                                                        'bg-gray-100 text-gray-600'
                                                    "
                                                >
                                                    {{ user.role }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <time :datetime="user.created_at">
                                                    {{ formatDate(user.created_at) }}
                                                </time>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <Link
                                                    :href="route('admin.users.edit', user.id)"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800 focus:rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    :aria-label="`Edit ${user.name}`"
                                                >
                                                    Edit
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <nav
                            v-if="users.links.length > 3"
                            class="mt-6 flex justify-center gap-1"
                            aria-label="Pagination"
                        >
                            <template v-for="link in users.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :class="
                                        link.active
                                            ? 'bg-indigo-600 font-semibold text-white'
                                            : 'text-gray-600 hover:bg-gray-100'
                                    "
                                    :aria-current="link.active ? 'page' : undefined"
                                >
                                    {{ decodeLabel(link.label) }}
                                </Link>
                                <span
                                    v-else
                                    class="rounded px-3 py-1.5 text-sm text-gray-300"
                                    aria-disabled="true"
                                >
                                    {{ decodeLabel(link.label) }}
                                </span>
                            </template>
                        </nav>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
