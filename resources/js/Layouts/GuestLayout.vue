<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { CheckCircleIcon } from '@heroicons/vue/24/solid';

withDefaults(
    defineProps<{
        title?: string;
        subtitle?: string;
    }>(),
    { title: undefined, subtitle: undefined }
);
</script>

<template>
    <div class="flex min-h-screen">
        <!-- LEFT PANEL — decorative brand copy, hidden below lg -->
        <aside
            aria-hidden="true"
            class="hidden lg:flex lg:w-5/12 lg:flex-col lg:justify-between relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 p-12 text-white"
        >
            <!-- Decorative blobs -->
            <div
                class="pointer-events-none absolute -left-16 -top-16 h-64 w-64 rounded-full bg-cyan-400 opacity-20 blur-3xl"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -bottom-16 -right-16 h-64 w-64 rounded-full bg-blue-300 opacity-20 blur-3xl"
                aria-hidden="true"
            />
            <!-- Dot pattern -->
            <div
                class="bg-dot-pattern pointer-events-none absolute inset-0 opacity-10"
                aria-hidden="true"
            />

            <!-- Top: logo + wordmark -->
            <div class="flex items-center gap-3 relative z-10">
                <ApplicationLogo class="h-10 w-10" />
                <span class="text-2xl font-bold tracking-tight">Eduno</span>
            </div>

            <!-- Middle: tagline + feature bullets -->
            <div class="relative z-10">
                <h2 class="text-3xl font-bold leading-snug">Empower Learning<br />Everywhere</h2>
                <p class="mt-3 text-sm text-blue-100 leading-relaxed">
                    The modern LMS for schools, teams, and organizations.
                </p>
                <ul class="mt-8 space-y-3 text-sm text-blue-50" role="list">
                    <li class="flex items-start gap-3">
                        <CheckCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-cyan-300"
                            aria-hidden="true"
                        />
                        Manage courses, modules, and assignments
                    </li>
                    <li class="flex items-start gap-3">
                        <CheckCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-cyan-300"
                            aria-hidden="true"
                        />
                        Submit work and track grades in real time
                    </li>
                    <li class="flex items-start gap-3">
                        <CheckCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-cyan-300"
                            aria-hidden="true"
                        />
                        WCAG 2.2 AA accessible for every learner
                    </li>
                </ul>

                <!-- Mini stat card -->
                <div
                    class="decorative mt-10 rounded-xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-200">
                        Today on Eduno
                    </p>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-white/10 p-2 text-center">
                            <p class="text-lg font-bold text-white">847</p>
                            <p class="text-[10px] text-blue-200">Students</p>
                        </div>
                        <div class="rounded-lg bg-white/10 p-2 text-center">
                            <p class="text-lg font-bold text-white">124</p>
                            <p class="text-[10px] text-blue-200">Courses</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="relative z-10 text-xs text-blue-200">
                Eduno · Modern Learning Management System
            </p>
        </aside>

        <!-- RIGHT PANEL — white form area -->
        <div class="flex flex-1 flex-col bg-white min-h-screen">
            <!-- Top gradient accent bar -->
            <div class="h-1 bg-gradient-to-r from-blue-600 to-cyan-500" aria-hidden="true" />

            <div
                class="flex flex-1 flex-col items-center justify-center px-6 py-12 sm:px-10 lg:px-16"
            >
                <!-- Mobile-only logo -->
                <div class="mb-8 lg:hidden">
                    <Link href="/" aria-label="Go to Eduno homepage">
                        <ApplicationLogo class="h-14 w-14 fill-current text-blue-600" />
                    </Link>
                </div>

                <!-- Optional form heading -->
                <div v-if="title || subtitle" class="mb-8 w-full max-w-md">
                    <h1 v-if="title" class="text-2xl font-bold text-gray-900">{{ title }}</h1>
                    <p v-if="subtitle" class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
                </div>

                <!-- Form slot -->
                <div class="w-full max-w-md">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
