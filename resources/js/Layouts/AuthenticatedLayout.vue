<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Bars3Icon, XMarkIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import type { PageProps } from '@/types';

const showingNavigationDropdown = ref(false);

const page = usePage<PageProps>();
const prefs = computed(() => page.props.auth?.preferences);
const userRole = computed(() => page.props.auth?.user?.role);
const features = computed(() => page.props.features);

watchEffect(() => {
    const html = document.documentElement;
    const p = prefs.value;
    const f = features.value;

    html.classList.remove('font-small', 'font-medium', 'font-large', 'font-xlarge');
    html.classList.add(`font-${p?.font_size ?? 'medium'}`);

    html.classList.toggle('high-contrast', f?.['high-contrast'] ?? p?.high_contrast ?? false);
    html.classList.toggle('reduce-motion', p?.reduced_motion ?? false);
    html.classList.toggle('simplified', f?.['simplified-layout'] ?? p?.simplified_layout ?? false);
});
</script>

<template>
    <div>
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 focus:px-4 focus:py-2 focus:bg-white focus:text-blue-700 focus:rounded focus:shadow-lg"
        >
            Skip to main content
        </a>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Notification Bell -->
                            <NotificationBell />

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger="{ open }">
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                aria-haspopup="true"
                                                :aria-expanded="open"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <ChevronDownIcon
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.accessibility.edit')">
                                            Accessibility
                                        </DropdownLink>
                                        <template v-if="userRole === 'admin'">
                                            <DropdownLink :href="route('admin.users.index')">
                                                Users
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.courses.index')">
                                                Courses
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.reports.index')">
                                                Reports
                                            </DropdownLink>
                                            <DropdownLink :href="route('admin.audit-logs.index')">
                                                Audit Logs
                                            </DropdownLink>
                                        </template>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                aria-label="Toggle navigation"
                                :aria-expanded="showingNavigationDropdown"
                                aria-controls="responsive-nav"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                            >
                                <XMarkIcon
                                    v-if="showingNavigationDropdown"
                                    class="h-6 w-6"
                                    aria-hidden="true"
                                />
                                <Bars3Icon v-else class="h-6 w-6" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    id="responsive-nav"
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.accessibility.edit')">
                                Accessibility
                            </ResponsiveNavLink>
                            <template v-if="userRole === 'admin'">
                                <ResponsiveNavLink :href="route('admin.users.index')">
                                    Users
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.courses.index')">
                                    Courses
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.reports.index')">
                                    Reports
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.audit-logs.index')">
                                    Audit Logs
                                </ResponsiveNavLink>
                            </template>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main id="main-content">
                <slot />
            </main>
        </div>
    </div>
</template>
