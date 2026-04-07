<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { UserIcon, LockClosedIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

type Tab = 'account' | 'password' | 'danger';

const activeTab = ref<Tab>('account');

const tabs: { key: Tab; label: string; icon: typeof UserIcon }[] = [
    { key: 'account', label: 'Account', icon: UserIcon },
    { key: 'password', label: 'Password', icon: LockClosedIcon },
    { key: 'danger', label: 'Danger Zone', icon: ExclamationTriangleIcon },
];
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Profile
            </h1>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Tab navigation -->
            <div
                class="mb-6 flex gap-1 overflow-hidden rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                role="tablist"
                aria-label="Profile sections"
            >
                <button
                    v-for="tab in tabs"
                    :id="`tab-${tab.key}`"
                    :key="tab.key"
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === tab.key"
                    :aria-controls="`tabpanel-${tab.key}`"
                    class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    :class="
                        activeTab === tab.key
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-slate-700'
                    "
                    @click="activeTab = tab.key"
                >
                    <component :is="tab.icon" class="h-4 w-4" aria-hidden="true" />
                    <span>{{ tab.label }}</span>
                </button>
            </div>

            <!-- Account panel -->
            <div
                v-show="activeTab === 'account'"
                id="tabpanel-account"
                role="tabpanel"
                aria-labelledby="tab-account"
            >
                <div
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <div
                        class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                    >
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Account Information
                        </h2>
                    </div>
                    <div class="px-6 py-6">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <div
                    class="mt-4 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <div
                        class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                    >
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Accessibility Preferences
                        </h2>
                    </div>
                    <div class="px-6 py-5">
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Adjust font size, contrast, and motion settings to suit your needs.
                        </p>
                        <Link
                            :href="route('profile.accessibility.edit')"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600"
                        >
                            Manage Accessibility Settings
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Password panel -->
            <div
                v-show="activeTab === 'password'"
                id="tabpanel-password"
                role="tabpanel"
                aria-labelledby="tab-password"
            >
                <div
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                >
                    <div
                        class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                    >
                        <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Change Password</h2>
                    </div>
                    <div class="px-6 py-6">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>
            </div>

            <!-- Danger Zone panel -->
            <div
                v-show="activeTab === 'danger'"
                id="tabpanel-danger"
                role="tabpanel"
                aria-labelledby="tab-danger"
            >
                <div
                    class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-red-100 dark:bg-slate-800 dark:ring-red-900/50"
                >
                    <div
                        class="flex items-center gap-3 border-b border-red-100 px-6 py-4 dark:border-red-900/50"
                    >
                        <div class="h-4 w-1 rounded-full bg-red-500" aria-hidden="true"></div>
                        <h2 class="font-semibold text-red-700">Danger Zone</h2>
                    </div>
                    <div class="px-6 py-6">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
