<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);

const features = [
    {
        title: 'Course Management',
        description:
            'Instructors can create, organize, and publish courses with structured modules and lessons.',
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    },
    {
        title: 'Interactive Modules',
        description:
            'Engage with rich learning materials including videos, documents, and interactive content.',
        icon: 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z',
    },
    {
        title: 'Smart Assignments',
        description:
            'Submit assignments online with file uploads, and track submission status in real time.',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
        title: 'Instant Grading',
        description:
            'Instructors grade submissions quickly and students receive immediate feedback.',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    },
    {
        title: 'Announcements',
        description:
            'Stay informed with course and system-wide announcements delivered directly to your dashboard.',
        icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
    },
    {
        title: 'Progress Tracking',
        description:
            'Monitor your learning journey with detailed progress reports and grade summaries.',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    },
];

const steps = [
    {
        number: '1',
        title: 'Enroll',
        description:
            'Create your account and enroll in courses offered by the University of Caloocan City.',
    },
    {
        number: '2',
        title: 'Learn',
        description:
            'Access lessons, complete assignments, and engage with your instructors online.',
    },
    {
        number: '3',
        title: 'Succeed',
        description: 'Track your grades, receive feedback, and achieve your academic goals.',
    },
];
</script>

<template>
    <Head title="Welcome to Eduno" />
    <div class="min-h-screen bg-white text-gray-900">
        <!-- Skip link (WCAG 2.4.1) -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-indigo-600 focus:px-4 focus:py-2 focus:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600"
        >
            Skip to main content
        </a>

        <!-- Header -->
        <header
            role="banner"
            class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur-sm"
        >
            <nav
                aria-label="Site navigation"
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8"
            >
                <!-- Logo -->
                <Link
                    :href="route('/')"
                    aria-label="Eduno home"
                    class="flex items-center gap-2 rounded-md focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                >
                    <ApplicationLogo class="h-8 w-8" />
                    <span class="text-xl font-bold text-indigo-600">Eduno</span>
                </Link>

                <!-- Desktop nav -->
                <div class="hidden items-center gap-3 sm:flex">
                    <template v-if="canLogin">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="inline-flex min-h-[44px] items-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="inline-flex min-h-[44px] items-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex min-h-[44px] items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                            >
                                Register
                            </Link>
                        </template>
                    </template>
                </div>

                <!-- Mobile hamburger -->
                <button
                    type="button"
                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 sm:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-controls="mobile-menu"
                    aria-label="Toggle navigation menu"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            v-if="!mobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </nav>

            <!-- Mobile menu -->
            <div
                v-show="mobileMenuOpen"
                id="mobile-menu"
                class="border-t border-gray-200 sm:hidden"
            >
                <div class="space-y-1 px-4 py-2">
                    <template v-if="canLogin">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="flex min-h-[44px] items-center rounded-md px-3 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="flex min-h-[44px] items-center rounded-md px-3 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="flex min-h-[44px] items-center rounded-md px-3 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                            >
                                Register
                            </Link>
                        </template>
                    </template>
                </div>
            </div>
        </header>

        <main id="main-content">
            <!-- Hero section -->
            <section
                aria-labelledby="hero-heading"
                class="bg-gradient-to-b from-indigo-50 to-white px-4 py-16 text-center sm:px-6 sm:py-24 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h1
                        id="hero-heading"
                        class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl"
                    >
                        Learn Smarter at UCC
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        Eduno is the University of Caloocan City's digital learning platform —
                        bringing courses, assignments, and grades to your fingertips.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex min-h-[44px] w-full items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 sm:w-auto"
                        >
                            Get Started
                        </Link>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="inline-flex min-h-[44px] w-full items-center justify-center rounded-lg border border-indigo-600 px-6 py-3 text-base font-semibold text-indigo-600 hover:bg-indigo-50 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 sm:w-auto"
                        >
                            Sign In
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Features section -->
            <section aria-labelledby="features-heading" class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <h2
                        id="features-heading"
                        class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Everything You Need to Learn
                    </h2>
                    <ul
                        role="list"
                        class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <li
                            v-for="feature in features"
                            :key="feature.title"
                            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100"
                            >
                                <svg
                                    class="h-6 w-6 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        :d="feature.icon"
                                    />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">
                                {{ feature.title }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ feature.description }}
                            </p>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- How it works section -->
            <section
                aria-labelledby="howitworks-heading"
                class="bg-indigo-50 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-7xl">
                    <h2
                        id="howitworks-heading"
                        class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        How It Works
                    </h2>
                    <ol class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-3">
                        <li
                            v-for="step in steps"
                            :key="step.number"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-xl font-bold text-white"
                                aria-hidden="true"
                            >
                                {{ step.number }}
                            </div>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">
                                {{ step.title }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ step.description }}
                            </p>
                        </li>
                    </ol>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 bg-white px-4 py-8 sm:px-6 lg:px-8">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <p class="text-sm text-gray-500">
                    &copy; 2026 University of Caloocan City. All rights reserved.
                </p>
                <nav aria-label="Footer navigation" class="flex gap-4">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="inline-flex min-h-[44px] items-center text-sm text-gray-500 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-flex min-h-[44px] items-center text-sm text-gray-500 hover:text-indigo-600 focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    >
                        Register
                    </Link>
                </nav>
            </div>
        </footer>
    </div>
</template>
