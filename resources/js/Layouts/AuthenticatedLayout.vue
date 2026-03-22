<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue';
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
} from '@heroicons/vue/24/outline';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();
const prefs = computed(() => page.props.userPrefs);
const userRole = computed(() => page.props.auth?.user?.role);
const features = computed(() => page.props.features);

const sidebarOpen = ref(false);

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
                'fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-800 transition-transform duration-300 ease-in-out',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <!-- Logo row -->
            <div class="flex h-16 shrink-0 items-center justify-between px-4">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <ApplicationLogo class="h-8 w-auto fill-current text-white" />
                    <span class="text-lg font-bold text-white">Eduno</span>
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
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <ul role="list" class="space-y-1">
                    <li v-for="item in navItems" :key="item.label">
                        <Link
                            :href="item.href"
                            :aria-current="route().current(item.routeName) ? 'page' : undefined"
                            :class="[
                                'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors',
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
                            {{ item.label }}
                        </Link>
                    </li>
                </ul>
            </nav>

            <!-- User section -->
            <div class="shrink-0 border-t border-slate-700 px-3 py-4">
                <div class="mb-3 px-3">
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
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white"
                        >
                            <UserCircleIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                            Profile
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('profile.accessibility.edit')"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white"
                        >
                            <Cog6ToothIcon class="h-5 w-5 shrink-0" aria-hidden="true" />
                            Accessibility
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-slate-700 hover:text-white"
                        >
                            <ArrowRightOnRectangleIcon
                                class="h-5 w-5 shrink-0"
                                aria-hidden="true"
                            />
                            Log Out
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
        <div class="flex flex-col lg:pl-64">
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
