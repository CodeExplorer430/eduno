<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    BookOpenIcon,
    ClipboardDocumentListIcon,
    ChartBarIcon,
    AcademicCapIcon,
    UsersIcon,
    Cog6ToothIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="Welcome to Eduno" />

    <div class="flex min-h-screen flex-col bg-white text-gray-900">
        <!-- Navbar -->
        <nav class="border-b border-gray-200 bg-white" aria-label="Main navigation">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <ApplicationLogo class="h-9 w-9" />
                    <span class="text-xl font-bold text-gray-900">Eduno</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-2">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 transition hover:text-blue-600 focus-visible:ring-2 focus-visible:ring-blue-300"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 transition hover:text-blue-600 focus-visible:ring-2 focus-visible:ring-blue-300"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus-visible:ring-2 focus-visible:ring-blue-300 focus-visible:ring-offset-2"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main id="main-content">
            <!-- Hero -->
            <section
                class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 py-24 text-white"
                aria-labelledby="hero-heading"
            >
                <div
                    class="bg-dot-pattern pointer-events-none absolute inset-0"
                    aria-hidden="true"
                />
                <div
                    class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-cyan-400 opacity-20 blur-3xl"
                    aria-hidden="true"
                />
                <div
                    class="pointer-events-none absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-blue-300 opacity-20 blur-3xl"
                    aria-hidden="true"
                />
                <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
                    <h1
                        id="hero-heading"
                        class="text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl"
                    >
                        Empower Learning at UCC
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-blue-100">
                        Your complete Learning Management System for the University of Caloocan
                        City. Manage courses, submit assignments, and track your progress — all in
                        one place.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-xl bg-white px-8 py-3 text-base font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-700"
                        >
                            Get Started — free
                        </Link>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="flex items-center gap-2 rounded-xl border border-white/50 px-8 py-3 text-base font-semibold text-white transition hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-700"
                        >
                            Log in to your account
                            <ArrowRightIcon class="h-4 w-4" aria-hidden="true" />
                        </Link>
                    </div>

                    <!-- Decorative dashboard preview -->
                    <div
                        aria-hidden="true"
                        class="decorative mx-auto mt-12 max-w-sm rounded-2xl border border-white/20 bg-white/10 p-5 text-left shadow-xl backdrop-blur-sm ring-1 ring-white/10"
                    >
                        <div class="mb-4 grid grid-cols-3 gap-3">
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-blue-200"
                                >
                                    Courses
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">12</p>
                            </div>
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-blue-200"
                                >
                                    Due Soon
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">5</p>
                            </div>
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-blue-200"
                                >
                                    Grade
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">94%</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 flex justify-between text-[10px] text-blue-200">
                                <span>Course Progress</span>
                                <span>78%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-white/20">
                                <div class="h-1.5 w-[78%] rounded-full bg-cyan-300" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats strip -->
            <div class="border-b border-gray-100 bg-white" aria-label="Platform at a glance">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <dl class="grid grid-cols-3 divide-x divide-gray-100 py-6 text-center">
                        <div class="px-4">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                Functional Reqs
                            </dt>
                            <dd class="mt-1 text-3xl font-bold text-gray-900">48</dd>
                        </div>
                        <div class="px-4">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                User Roles
                            </dt>
                            <dd class="mt-1 text-3xl font-bold text-gray-900">3</dd>
                        </div>
                        <div class="px-4">
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                Accessibility
                            </dt>
                            <dd class="mt-1 text-3xl font-bold text-blue-600">WCAG 2.2 AA</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Features -->
            <section class="bg-slate-50 py-20" aria-labelledby="features-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 id="features-heading" class="text-center text-3xl font-bold text-gray-900">
                        Everything you need in one platform
                    </h2>
                    <div class="mt-12 grid gap-8 sm:grid-cols-3">
                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 0ms`"
                            class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-1 hover:shadow-md motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50"
                            >
                                <BookOpenIcon class="h-6 w-6 text-blue-600" aria-hidden="true" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Manage Courses</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                                Create and organize courses with modules and lessons. Instructors
                                have full control over course content and student enrollment.
                            </p>
                        </article>

                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 100ms`"
                            class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-1 hover:shadow-md motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-cyan-50"
                            >
                                <ClipboardDocumentListIcon
                                    class="h-6 w-6 text-cyan-500"
                                    aria-hidden="true"
                                />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Assignments &amp; Submissions
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                                Create assignments with deadlines, accept file submissions, and
                                release grades with per-student feedback.
                            </p>
                        </article>

                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 200ms`"
                            class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-1 hover:shadow-md motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-50"
                            >
                                <ChartBarIcon class="h-6 w-6 text-amber-500" aria-hidden="true" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Track Progress</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                                Students and instructors get a real-time dashboard showing grades,
                                submission status, and course completion at a glance.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- For Each Role -->
            <section class="bg-gray-100 py-20" aria-labelledby="roles-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 id="roles-heading" class="text-center text-3xl font-bold text-gray-900">
                        Built for every role
                    </h2>
                    <div class="mt-12 grid gap-6 sm:grid-cols-3">
                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 0ms`"
                            class="rounded-xl border-l-4 border-blue-600 bg-blue-50/40 p-6 shadow-sm"
                        >
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                                <AcademicCapIcon class="h-5 w-5 text-blue-600" aria-hidden="true" />
                                Students
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                                <li>Browse and enroll in courses</li>
                                <li>Submit assignments and view grades</li>
                                <li>Access learning materials anytime</li>
                                <li>Receive announcements and notifications</li>
                            </ul>
                        </div>

                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 100ms`"
                            class="rounded-xl border-l-4 border-cyan-500 bg-cyan-50/40 p-6 shadow-sm"
                        >
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                                <UsersIcon class="h-5 w-5 text-cyan-500" aria-hidden="true" />
                                Instructors
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                                <li>Create and manage course content</li>
                                <li>Design assignments with rubrics</li>
                                <li>Grade submissions and release feedback</li>
                                <li>Post announcements to students</li>
                            </ul>
                        </div>

                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 200ms`"
                            class="rounded-xl border-l-4 border-amber-500 bg-amber-50/40 p-6 shadow-sm"
                        >
                            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                                <Cog6ToothIcon class="h-5 w-5 text-amber-500" aria-hidden="true" />
                                Admins
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-gray-600">
                                <li>Manage users and role assignments</li>
                                <li>Oversee all courses and enrollments</li>
                                <li>View audit logs and activity reports</li>
                                <li>Configure system-wide feature flags</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Banner -->
            <section
                v-animateonscroll="{ enterClass: 'animate-fadein' }"
                class="bg-gradient-to-r from-blue-600 to-cyan-600 py-16 text-white"
                aria-labelledby="cta-heading"
            >
                <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
                    <h2 id="cta-heading" class="text-3xl font-bold">Ready to get started?</h2>
                    <p class="mt-4 text-lg text-blue-100">
                        Join students and instructors at the University of Caloocan City on Eduno.
                    </p>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="mt-8 inline-block rounded-xl bg-white px-8 py-3 text-base font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-700"
                    >
                        Sign Up for Free
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white py-8 text-center text-sm text-gray-500">
            <p>© 2026 University of Caloocan City · Eduno LMS</p>
            <p class="mt-1 text-xs text-gray-400">Empowering Learners, Inspiring Excellence</p>
        </footer>
    </div>
</template>
