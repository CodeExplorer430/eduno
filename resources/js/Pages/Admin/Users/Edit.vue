<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    user: { id: number; name: string; email: string; role: string };
    roles: Array<{ name: string; value: string }>;
}

const props = defineProps<Props>();

const form = useForm<{ role: string }>({
    role: props.user.role,
});

const submit = (): void => {
    form.patch(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head :title="`Edit User — ${user.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('admin.users.index')"
                            class="hover:text-gray-700 focus:rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            Users
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ user.name }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-lg sm:px-6 lg:px-8">
                <main>
                    <section
                        aria-labelledby="edit-user-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1 id="edit-user-heading" class="text-lg font-bold text-gray-900">
                                Edit User Role
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">
                                {{ user.name }} &middot;
                                <span class="text-gray-400">{{ user.email }}</span>
                            </p>
                        </header>

                        <form novalidate @submit.prevent="submit">
                            <div class="px-6 py-6">
                                <div>
                                    <InputLabel for="user-role">
                                        Role
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <select
                                        id="user-role"
                                        v-model="form.role"
                                        aria-describedby="user-role-error"
                                        :aria-invalid="!!form.errors.role"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    >
                                        <option v-for="r in roles" :key="r.value" :value="r.value">
                                            {{ r.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        id="user-role-error"
                                        class="mt-1"
                                        :message="form.errors.role"
                                    />
                                </div>
                            </div>

                            <footer
                                class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                            >
                                <Link :href="route('admin.users.index')">
                                    <Button type="button" severity="secondary">Cancel</Button>
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    :aria-busy="form.processing"
                                >
                                    <span v-if="form.processing">Saving&hellip;</span>
                                    <span v-else>Save Role</span>
                                </Button>
                            </footer>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
