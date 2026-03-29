<script setup lang="ts">
import { ref, computed, watchEffect, onMounted, watch } from 'vue';
import type { Component } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { Link, usePage } from '@inertiajs/vue3';
import { trans as t } from 'laravel-vue-i18n';
import {
    HomeIcon,
    BookOpenIcon,
    UsersIcon,
    DocumentTextIcon,
    ChartBarIcon,
    MegaphoneIcon,
    ClipboardDocumentListIcon,
    AcademicCapIcon,
    Bars3Icon,
    XMarkIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon,
    UserCircleIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    FlagIcon,
} from '@heroicons/vue/24/outline';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();
const prefs = computed(() => page.props.userPrefs);
const userRole = computed(() => page.props.auth?.user?.role);
const features = computed(() => page.props.features);
const toast = useToast();

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false); // SSR-safe default

onMounted(() => {
    sidebarCollapsed.value = localStorage.getItem('eduno:sidebar-collapsed') === 'true';
});

function toggleCollapse(): void {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('eduno:sidebar-collapsed', String(sidebarCollapsed.value));
}

interface NavItem {
    label: string;
    href: string;
    icon: Component;
    routeName: string;
}

function safeRoute(name: string): string {
    try {
        return route(name);
    } catch {
        return '#';
    }
}

const navItems = computed<NavItem[]>(() => {
    const role = userRole.value;
    const base: NavItem[] = [
        {
            label: t('nav.dashboard'),
            href: route('dashboard'),
            icon: HomeIcon,
            routeName: 'dashboard',
        },
    ];

    if (role === 'student') {
        return [
            ...base,
            {
                label: t('nav.my_courses'),
                href: safeRoute('student.courses.index'),
                icon: BookOpenIcon,
                routeName: 'student.courses.*',
            },
            {
                label: t('nav.assignments'),
                href: safeRoute('student.assignments.index'),
                icon: ClipboardDocumentListIcon,
                routeName: 'student.assignments.*',
            },
            {
                label: t('nav.grades'),
                href: safeRoute('student.grades.index'),
                icon: AcademicCapIcon,
                routeName: 'student.grades.*',
            },
            {
                label: t('nav.announcements'),
                href: safeRoute('student.announcements.index'),
                icon: MegaphoneIcon,
                routeName: 'student.announcements.*',
            },
        ];
    }

    if (role === 'instructor') {
        return [
            ...base,
            {
                label: t('nav.my_courses'),
                href: safeRoute('instructor.courses.index'),
                icon: BookOpenIcon,
                routeName: 'instructor.courses.*',
            },
            {
                label: t('nav.submissions'),
                href: safeRoute('instructor.submissions.all'),
                icon: DocumentTextIcon,
                routeName: 'instructor.submissions.*',
            },
            {
                label: t('nav.announcements'),
                href: safeRoute('instructor.announcements.index'),
                icon: MegaphoneIcon,
                routeName: 'instructor.announcements.*',
            },
        ];
    }

    return [
        ...base,
        {
            label: t('nav.users'),
            href: safeRoute('admin.users.index'),
            icon: UsersIcon,
            routeName: 'admin.users.*',
        },
        {
            label: t('nav.courses'),
            href: safeRoute('admin.courses.index'),
            icon: BookOpenIcon,
            routeName: 'admin.courses.*',
        },
        {
            label: t('nav.reports'),
            href: safeRoute('admin.reports.index'),
            icon: ChartBarIcon,
            routeName: 'admin.reports.*',
        },
        {
            label: t('nav.audit_logs'),
            href: safeRoute('admin.audit-logs.index'),
            icon: ClipboardDocumentListIcon,
            routeName: 'admin.audit-logs.*',
        },
        {
            label: 'Flagged',
            href: safeRoute('admin.flagged-submissions.index'),
            icon: FlagIcon,
            routeName: 'admin.flagged-submissions.*',
        },
        {
            label: t('nav.settings'),
            href: safeRoute('admin.settings.index'),
            icon: Cog6ToothIcon,
            routeName: 'admin.settings.*',
        },
    ];
});

watch(
    () => page.props.flash as Record<string, string> | undefined,
    (flash) => {
        if (!flash) return;
        if (flash.success)
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: flash.success,
                life: 3500,
            });
        if (flash.error)
            toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 5000 });
        if (flash.info)
            toast.add({ severity: 'info', summary: 'Info', detail: flash.info, life: 3500 });
        if (flash.warning)
            toast.add({ severity: 'warn', summary: 'Warning', detail: flash.warning, life: 4000 });
    }
);

watchEffect(() => {
    const html = document.documentElement;
    const p = prefs.value;
    const f = features.value;

    html.classList.remove('font-small', 'font-medium', 'font-large', 'font-xlarge');
    html.classList.add(`font-${p?.font_size ?? 'medium'}`);

    html.classList.toggle('high-contrast', f?.['high-contrast'] ?? p?.high_contrast ?? false);
    html.classList.toggle('reduce-motion', p?.reduced_motion ?? false);
    html.classList.toggle('simplified', f?.['simplified-layout'] ?? p?.simplified_layout ?? false);
    html.classList.toggle('dyslexia-font', p?.dyslexia_font ?? false);
    html.classList.toggle('dark', p?.dark_mode ?? false);
});
</script>

<template>
    <div>
        <Toast position="top-right" />

        <!-- Skip link — first focusable element -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-blue-700 focus:shadow-lg"
        >
            Skip to main content
        </a>

        <!-- Sidebar -->
        <aside
            id="sidebar"
            aria-label="Main navigation"
            :class="[
                'fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-800 transition-all duration-300 ease-in-out overflow-visible',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarCollapsed ? 'lg:w-16' : 'lg:w-64',
                'w-64',
            ]"
        >
            <!-- Logo row -->
            <div class="flex h-16 shrink-0 items-center justify-between px-4">
                <Link :href="route('dashboard')" class="flex items-center gap-2 overflow-hidden">
                    <ApplicationLogo class="h-8 w-8 shrink-0 fill-current text-white" />
                    <span
                        class="overflow-hidden text-lg font-bold text-white transition-all duration-300"
                        :class="sidebarCollapsed ? 'lg:hidden' : 'lg:block'"
                    >
                        Eduno
                    </span>
                </Link>
                <button
                    type="button"
                    class="rounded-md p-1 text-slate-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white lg:hidden"
                    aria-label="Close navigation"
                    @click="sidebarOpen = false"
                >
                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                </button>
            </div>

            <!-- Navigation links -->
            <nav class="flex flex-1 flex-col overflow-y-auto px-3 py-4">
                <ul role="list" class="flex-1 space-y-1">
                    <li v-for="item in navItems" :key="item.label">
                        <Link
                            :href="item.href"
                            :title="sidebarCollapsed ? item.label : undefined"
                            :aria-current="route().current(item.routeName) ? 'page' : undefined"
                            :class="[
                                'flex items-center rounded-md py-2 text-sm font-medium transition-colors',
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : 'gap-3 px-3',
                                route().current(item.routeName)
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-300 hover:bg-slate-700 hover:text-white',
                            ]"
                        >
                            <component
                                :is="item.icon"
                                class="h-5 w-5 shrink-0"
                                aria-hidden="true"
                            />
                            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">{{
                                item.label
                            }}</span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <!-- Collapse toggle — floating tab at sidebar right edge (desktop only) -->
            <button
                type="button"
                class="absolute -right-3 top-[4.5rem] hidden h-6 w-6 items-center justify-center rounded-full border border-slate-600 bg-slate-700 text-slate-300 shadow-md transition-colors hover:bg-slate-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-white lg:flex"
                :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                @click="toggleCollapse"
            >
                <ChevronLeftIcon v-if="!sidebarCollapsed" class="h-3.5 w-3.5" aria-hidden="true" />
                <ChevronRightIcon v-else class="h-3.5 w-3.5" aria-hidden="true" />
            </button>

            <!-- User section -->
            <div class="shrink-0 border-t border-slate-700 px-3 py-4">
                <div class="mb-3 px-3" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="text-sm font-medium text-white">
                        {{ $page.props.auth.user.name }}
                    </p>
                    <p class="text-xs capitalize text-slate-400">
                        {{ $page.props.auth.user.role }}
                    </p>
                </div>
                <ul role="list" class="space-y-1">
                    <li>
                        <Link
                            :href="route('profile.edit')"
                            :title="sidebarCollapsed ? 'Profile' : undefined"
                            :class="[
                                'flex items-center rounded-md py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white',
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : 'gap-3 px-3',
                            ]"
                        >
                            <UserCircleIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Profile</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('profile.accessibility.edit')"
                            :title="sidebarCollapsed ? 'Accessibility' : undefined"
                            :class="[
                                'flex items-center rounded-md py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white',
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : 'gap-3 px-3',
                            ]"
                        >
                            <Cog6ToothIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Accessibility</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            :title="sidebarCollapsed ? 'Log Out' : undefined"
                            :class="[
                                'flex w-full items-center rounded-md py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white',
                                sidebarCollapsed ? 'lg:justify-center lg:px-2' : 'gap-3 px-3',
                            ]"
                        >
                            <ArrowRightOnRectangleIcon
                                class="h-5 w-5 shrink-0"
                                aria-hidden="true"
                            />
                            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Log Out</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            aria-hidden="true"
            @click="sidebarOpen = false"
        />

        <!-- Content area (offset by sidebar on lg+) -->
        <div
            :class="[
                'flex flex-col transition-all duration-300',
                sidebarCollapsed ? 'lg:pl-16' : 'lg:pl-64',
            ]"
        >
            <!-- Top header -->
            <header
                class="sticky top-0 z-30 flex h-16 items-center gap-4 bg-white px-4 shadow-sm sm:px-6 dark:bg-slate-900 dark:shadow-slate-800"
            >
                <!-- Hamburger (mobile only) -->
                <button
                    type="button"
                    class="rounded-md p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden dark:text-slate-300 dark:hover:text-white"
                    :aria-expanded="sidebarOpen"
                    aria-controls="sidebar"
                    aria-label="Open navigation"
                    @click="sidebarOpen = true"
                >
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <!-- Page-level heading slot -->
                <div class="flex-1">
                    <slot name="header" />
                </div>

                <!-- Notification bell -->
                <NotificationBell />
            </header>

            <!-- Page content -->
            <main
                id="main-content"
                class="flex-1 min-w-0 overflow-x-hidden bg-slate-50 pb-16 lg:pb-0 dark:bg-slate-950"
            >
                <slot />
            </main>
        </div>

        <!-- Mobile bottom navigation -->
        <nav
            aria-label="Mobile bottom navigation"
            class="fixed bottom-0 left-0 right-0 z-40 flex h-16 items-stretch bg-slate-800 lg:hidden"
            style="padding-bottom: env(safe-area-inset-bottom)"
        >
            <Link
                v-for="item in navItems.slice(0, 4)"
                :key="item.label"
                :href="item.href"
                :aria-current="route().current(item.routeName) ? 'page' : undefined"
                :class="[
                    'flex flex-1 flex-col items-center justify-center gap-0.5 text-xs font-medium transition-colors',
                    route().current(item.routeName) ? 'text-blue-400' : 'text-slate-400',
                ]"
            >
                <component :is="item.icon" class="h-5 w-5 shrink-0" aria-hidden="true" />
                <span>{{ item.label }}</span>
            </Link>
        </nav>
    </div>
</template>
