<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    user: { id: number; name: string; email: string; role: string };
    roles: Array<{ name: string; value: string }>;
}

const props = defineProps<Props>();

const form = useForm<{ role: string }>({
    role: props.user.role,
});

const roleIcons: Record<string, string> = {
    student: '🎓',
    instructor: '📚',
    admin: '⚙️',
};

const roleDescriptions: Record<string, string> = {
    student: 'Can enroll in courses and submit assignments.',
    instructor: 'Can create courses, modules, and grade submissions.',
    admin: 'Full access to all system administration features.',
};

function getInitials(name: string): string {
    return name
        .split(' ')
        .slice(0, 2)
        .map((n) => n[0])
        .join('')
        .toUpperCase();
}

const submit = (): void => {
    form.patch(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head :title="`Edit User — ${user.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                    <li>
                        <Link
                            :href="route('admin.users.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-gray-200"
                        >
                            Users
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800 dark:text-gray-200" aria-current="page">
                        {{ user.name }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- User info card -->
                <aside
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <div class="flex flex-col items-center px-6 py-8 text-center">
                        <div
                            class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-700"
                            aria-hidden="true"
                        >
                            {{ getInitials(user.name) }}
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ user.name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                            {{ user.email }}
                        </p>
                        <span
                            class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize"
                            :class="{
                                'bg-purple-100 text-purple-700': user.role === 'admin',
                                'bg-blue-100 text-blue-700': user.role === 'instructor',
                                'bg-green-100 text-green-700': user.role === 'student',
                            }"
                        >
                            {{ user.role }}
                        </span>
                    </div>
                </aside>

                <!-- Edit form -->
                <section
                    aria-labelledby="edit-user-heading"
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 lg:col-span-2 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <div
                        class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                    >
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h1
                            id="edit-user-heading"
                            class="font-semibold text-gray-900 dark:text-white"
                        >
                            Edit User Role
                        </h1>
                    </div>

                    <form novalidate class="px-6 py-6" @submit.prevent="submit">
                        <fieldset>
                            <legend
                                class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Select Role
                                <span class="text-red-500" aria-hidden="true">*</span>
                                <span class="sr-only">(required)</span>
                            </legend>

                            <div class="space-y-3" aria-describedby="user-role-error">
                                <label
                                    v-for="r in roles"
                                    :key="r.value"
                                    class="flex cursor-pointer items-start gap-4 rounded-xl border-2 p-4 transition-colors"
                                    :class="
                                        form.role === r.value
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 dark:border-slate-600 dark:bg-slate-700 dark:hover:bg-slate-600'
                                    "
                                >
                                    <input
                                        v-model="form.role"
                                        type="radio"
                                        :value="r.value"
                                        class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600"
                                    />
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            <span aria-hidden="true">{{
                                                roleIcons[r.value] ?? '👤'
                                            }}</span>
                                            {{ r.name }}
                                        </p>
                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-slate-400">
                                            {{ roleDescriptions[r.value] ?? '' }}
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <InputError
                                id="user-role-error"
                                class="mt-2"
                                :message="form.errors.role"
                            />
                        </fieldset>

                        <div
                            class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-slate-700"
                        >
                            <Link
                                :href="route('admin.users.index')"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                :aria-busy="form.processing"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                            >
                                <span v-if="form.processing">Saving&hellip;</span>
                                <span v-else>Save Role</span>
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
