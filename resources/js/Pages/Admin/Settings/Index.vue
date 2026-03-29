<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Cog6ToothIcon, BellIcon, FlagIcon } from '@heroicons/vue/24/outline';

interface SettingsMap {
    general?: Record<string, string>;
    notifications?: Record<string, string>;
}

const props = defineProps<{ settings: SettingsMap }>();

type Tab = 'general' | 'notifications';
const activeTab = ref<Tab>('general');

const tabs: { key: Tab; label: string; icon: typeof Cog6ToothIcon }[] = [
    { key: 'general', label: 'General', icon: Cog6ToothIcon },
    { key: 'notifications', label: 'Notifications', icon: BellIcon },
];

const g = props.settings.general ?? {};
const n = props.settings.notifications ?? {};

const form = useForm({
    site_name: g['site_name'] ?? 'Eduno',
    registration_open: g['registration_open'] ?? 'true',
    maintenance_mode: g['maintenance_mode'] ?? 'false',
    email_notifications: n['email_notifications'] ?? 'true',
    deadline_reminder_hours: Number(n['deadline_reminder_hours'] ?? 24),
    email_digest: n['email_digest'] ?? 'false',
});

function submit(): void {
    form.patch(route('admin.settings.update'));
}

function boolVal(
    key: 'registration_open' | 'maintenance_mode' | 'email_notifications' | 'email_digest'
): boolean {
    return form[key] === 'true';
}

function setBool(
    key: 'registration_open' | 'maintenance_mode' | 'email_notifications' | 'email_digest',
    val: boolean
): void {
    form[key] = val ? 'true' : 'false';
}
</script>

<template>
    <Head title="Admin — Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Settings</h1>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Tab bar -->
            <div
                class="mb-6 flex gap-1 overflow-hidden rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                role="tablist"
                aria-label="Settings sections"
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

            <form novalidate @submit.prevent="submit">
                <!-- ── GENERAL ─────────────────────────────────── -->
                <div
                    v-show="activeTab === 'general'"
                    id="tabpanel-general"
                    role="tabpanel"
                    aria-labelledby="tab-general"
                >
                    <div
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-slate-800 dark:ring-slate-700"
                    >
                        <div
                            class="flex items-center gap-3 border-b border-gray-100 px-6 py-4 dark:border-slate-700"
                        >
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">
                                General Settings
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-100 px-6 dark:divide-slate-700">
                            <!-- Site name -->
                            <div class="py-4">
                                <label
                                    for="site_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Site Name
                                </label>
                                <input
                                    id="site_name"
                                    v-model="form.site_name"
                                    type="text"
                                    maxlength="100"
                                    class="mt-1 block w-full max-w-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                    :aria-describedby="
                                        form.errors.site_name ? 'site-name-error' : undefined
                                    "
                                />
                                <p
                                    v-if="form.errors.site_name"
                                    id="site-name-error"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.site_name }}
                                </p>
                            </div>

                            <!-- Student self-registration -->
                            <div class="flex items-center justify-between py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Student Self-Registration
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">
                                        Allow new students to register without an invitation.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="boolVal('registration_open')"
                                    aria-label="Student Self-Registration"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="
                                        boolVal('registration_open') ? 'bg-blue-600' : 'bg-gray-200'
                                    "
                                    @click="
                                        setBool('registration_open', !boolVal('registration_open'))
                                    "
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform"
                                        :class="
                                            boolVal('registration_open')
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                    ></span>
                                </button>
                            </div>

                            <!-- Maintenance mode -->
                            <div class="flex items-center justify-between py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Maintenance Mode
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">
                                        Take the site offline for non-admin users.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="boolVal('maintenance_mode')"
                                    aria-label="Maintenance Mode"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="
                                        boolVal('maintenance_mode') ? 'bg-red-600' : 'bg-gray-200'
                                    "
                                    @click="
                                        setBool('maintenance_mode', !boolVal('maintenance_mode'))
                                    "
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform"
                                        :class="
                                            boolVal('maintenance_mode')
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── NOTIFICATIONS ───────────────────────────── -->
                <div
                    v-show="activeTab === 'notifications'"
                    id="tabpanel-notifications"
                    role="tabpanel"
                    aria-labelledby="tab-notifications"
                >
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                            <h2 class="font-semibold text-gray-900">Notification Settings</h2>
                        </div>
                        <div class="divide-y divide-gray-100 px-6">
                            <!-- Email notifications -->
                            <div class="flex items-center justify-between py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email Notifications
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">
                                        Send email alerts for grades, deadlines, and announcements.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="boolVal('email_notifications')"
                                    aria-label="Email Notifications"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="
                                        boolVal('email_notifications')
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200'
                                    "
                                    @click="
                                        setBool(
                                            'email_notifications',
                                            !boolVal('email_notifications')
                                        )
                                    "
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform"
                                        :class="
                                            boolVal('email_notifications')
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                    ></span>
                                </button>
                            </div>

                            <!-- Deadline reminder lead time -->
                            <div class="py-4">
                                <label
                                    for="deadline_reminder_hours"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Deadline Reminder Lead Time
                                </label>
                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                    How far in advance to remind students of upcoming deadlines.
                                </p>
                                <select
                                    id="deadline_reminder_hours"
                                    v-model="form.deadline_reminder_hours"
                                    class="mt-2 block w-36 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                >
                                    <option :value="12">12 hours</option>
                                    <option :value="24">24 hours</option>
                                    <option :value="48">48 hours</option>
                                </select>
                            </div>

                            <!-- Email digest -->
                            <div class="flex items-center justify-between py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Announcement Email Digest
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">
                                        Send a digest email when new announcements are published.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="boolVal('email_digest')"
                                    aria-label="Announcement Email Digest"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    :class="boolVal('email_digest') ? 'bg-blue-600' : 'bg-gray-200'"
                                    @click="setBool('email_digest', !boolVal('email_digest'))"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform"
                                        :class="
                                            boolVal('email_digest')
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save button -->
                <div class="mt-5 flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :aria-busy="form.processing"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                    >
                        <span v-if="form.processing">Saving…</span>
                        <span v-else>Save Settings</span>
                    </button>
                </div>

                <div
                    v-if="form.wasSuccessful"
                    role="status"
                    aria-live="polite"
                    class="mt-3 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700"
                >
                    Settings saved successfully.
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
