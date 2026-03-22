<script setup lang="ts">
import { ref, computed, watchEffect, onMounted } from 'vue';
import type { Component } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import { Link, usePage } from '@inertiajs/vue3';
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
} from '@heroicons/vue/24/outline';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();
const prefs = computed(() => page.props.userPrefs);
const userRole = computed(() => page.props.auth?.user?.role);
const features = computed(() => page.props.features);

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
        { label: 'Dashboard', href: route('dashboard'), icon: HomeIcon, routeName: 'dashboard' },
    ];

    if (role === 'student') {
        return [
            ...base,
            {
                label: 'My Courses',
                href: safeRoute('student.courses.index'),
                icon: BookOpenIcon,
                routeName: 'student.courses.*',
            },
            {
                label: 'Assignments',
                href: safeRoute('student.assignments.index'),
                icon: ClipboardDocumentListIcon,
                routeName: 'student.assignments.*',
            },
            {
                label: 'Grades',
                href: safeRoute('student.grades.index'),
                icon: AcademicCapIcon,
                routeName: 'student.grades.*',
            },
            {
                label: 'Announcements',
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
                label: 'My Courses',
                href: safeRoute('instructor.courses.index'),
                icon: BookOpenIcon,
                routeName: 'instructor.courses.*',
            },
            {
                label: 'Submissions',
                href: safeRoute('instructor.submissions.index'),
                icon: DocumentTextIcon,
                routeName: 'instructor.submissions.*',
            },
            {
                label: 'Announcements',
                href: safeRoute('instructor.announcements.index'),
                icon: MegaphoneIcon,
                routeName: 'instructor.announcements.*',
            },
        ];
    }

    return [
        ...base,
        {
            label: 'Users',
            href: safeRoute('admin.users.index'),
            icon: UsersIcon,
            routeName: 'admin.users.*',
        },
        {
            label: 'Courses',
            href: safeRoute('admin.courses.index'),
            icon: BookOpenIcon,
            routeName: 'admin.courses.*',
        },
        {
            label: 'Reports',
            href: safeRoute('admin.reports.index'),
            icon: ChartBarIcon,
            routeName: 'admin.reports.*',
        },
        {
            label: 'Audit Logs',
            href: safeRoute('admin.audit-logs.index'),
            icon: ClipboardDocumentListIcon,
            routeName: 'admin.audit-logs.*',
        },
    ];
});

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
});
</script>

<template>
    <div>
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
                'fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-800 transition-all duration-300 ease-in-out',
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

                <!-- Collapse toggle (desktop only) -->
                <div class="hidden px-3 py-2 lg:block">
                    <button
                        type="button"
                        class="flex w-full items-center justify-center rounded-md p-2 text-slate-400 transition-colors hover:bg-slate-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                        :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                        @click="toggleCollapse"
                    >
                        <ChevronLeftIcon
                            v-if="!sidebarCollapsed"
                            class="h-4 w-4"
                            aria-hidden="true"
                        />
                        <ChevronRightIcon v-else class="h-4 w-4" aria-hidden="true" />
                    </button>
                </div>
            </nav>

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
                class="sticky top-0 z-30 flex h-16 items-center gap-4 bg-white px-4 shadow-sm sm:px-6"
            >
                <!-- Hamburger (mobile only) -->
                <button
                    type="button"
                    class="rounded-md p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden"
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
            <main id="main-content" class="flex-1 bg-slate-50">
                <slot />
            </main>
        </div>
    </div>
</template>
