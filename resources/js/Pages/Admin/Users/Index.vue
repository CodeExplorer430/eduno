<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';
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

interface Props {
    users: {
        data: User[];
        links: PaginationLink[];
    };
}

defineProps<Props>();

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
            <h1 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                <UserGroupIcon class="h-6 w-6 text-gray-700" aria-hidden="true" />
                User Management
            </h1>
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
                                                    class="inline-flex items-center gap-1 font-medium text-blue-600 hover:text-blue-800 focus:rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    :aria-label="`Edit ${user.name}`"
                                                >
                                                    <PencilSquareIcon
                                                        class="me-1 inline h-4 w-4"
                                                        aria-hidden="true"
                                                    />
                                                    Edit
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <Pagination v-if="users.links.length > 3" :links="users.links" />
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
