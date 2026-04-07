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

    <div class="flex min-h-screen flex-col bg-[#f9f9ff] text-[#141b2b]">
        <!-- Navbar -->
        <nav class="bg-[#f9f9ff]/80 backdrop-blur-[20px]" aria-label="Main navigation">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <ApplicationLogo class="h-9 w-9 fill-current text-[#00174b]" />
                    <span class="text-xl font-bold text-[#00174b]">Eduno</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-2">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-[#434655] transition hover:bg-[#f1f3ff] hover:text-[#141b2b] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#004ac6]"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-[#434655] transition hover:bg-[#f1f3ff] hover:text-[#141b2b] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#004ac6]"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-xl bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#004ac6] focus-visible:ring-offset-2"
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
                class="relative overflow-hidden bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] py-24 text-white"
                aria-labelledby="hero-heading"
            >
                <div
                    class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl decorative"
                    aria-hidden="true"
                />
                <div
                    class="pointer-events-none absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-white/10 blur-3xl decorative"
                    aria-hidden="true"
                />
                <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
                    <h1
                        id="hero-heading"
                        class="text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl"
                    >
                        Empower Learning<br />
                        <span class="text-white/90">Everywhere</span>
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-white/80">
                        The modern Learning Management System for schools, teams, and organizations.
                        Manage courses, submit assignments, and track your progress — all in one
                        place.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-xl bg-white px-8 py-3 text-base font-semibold text-[#004ac6] transition hover:bg-[#f1f3ff] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#004ac6]"
                        >
                            Get Started — free
                        </Link>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="flex items-center gap-2 rounded-xl border border-white/40 px-8 py-3 text-base font-semibold text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#004ac6]"
                        >
                            Log in to your account
                            <ArrowRightIcon class="h-4 w-4" aria-hidden="true" />
                        </Link>
                    </div>

                    <!-- Decorative dashboard preview -->
                    <div
                        aria-hidden="true"
                        class="animate-float decorative mx-auto mt-12 max-w-sm rounded-2xl bg-white/10 p-5 text-left shadow-2xl backdrop-blur-sm"
                    >
                        <div class="mb-4 grid grid-cols-3 gap-3">
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-white/70"
                                >
                                    Courses
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">12</p>
                            </div>
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-white/70"
                                >
                                    Due Soon
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">5</p>
                            </div>
                            <div class="rounded-lg bg-white/15 p-3">
                                <p
                                    class="text-[10px] font-medium uppercase tracking-wider text-white/70"
                                >
                                    Grade
                                </p>
                                <p class="mt-1 text-xl font-bold text-white">94%</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 flex justify-between text-[10px] text-white/70">
                                <span>Course Progress</span>
                                <span>78%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-white/20">
                                <div class="h-1.5 w-[78%] rounded-full bg-white/70" />
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div
                                class="flex items-center justify-between rounded-lg bg-white/10 px-3 py-2 text-xs text-white/80"
                            >
                                <span>CCS 123 — Final Project</span>
                                <span
                                    class="rounded-full bg-amber-400/30 px-2 py-0.5 text-amber-200"
                                    >Due tomorrow</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between rounded-lg bg-white/10 px-3 py-2 text-xs text-white/80"
                            >
                                <span>CCS 221 — Lab Activity 4</span>
                                <span class="rounded-full bg-white/20 px-2 py-0.5 text-white/80"
                                    >Submitted</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="bg-[#f1f3ff] py-20" aria-labelledby="features-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 id="features-heading" class="text-center text-3xl font-bold text-[#141b2b]">
                        Everything you need in one platform
                    </h2>
                    <div class="mt-12 grid gap-8 sm:grid-cols-3">
                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 0ms`"
                            class="rounded-2xl bg-white p-8 transition-all duration-200 hover:-translate-y-1 motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff]"
                            >
                                <BookOpenIcon class="h-6 w-6 text-[#004ac6]" aria-hidden="true" />
                            </div>
                            <h3 class="text-lg font-semibold text-[#141b2b]">Manage Courses</h3>
                            <p class="mt-2 text-sm leading-relaxed text-[#434655]">
                                Create and organize courses with modules and lessons. Instructors
                                have full control over course content and student enrollment.
                            </p>
                        </article>

                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 100ms`"
                            class="rounded-2xl bg-white p-8 transition-all duration-200 hover:-translate-y-1 motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff]"
                            >
                                <ClipboardDocumentListIcon
                                    class="h-6 w-6 text-[#004ac6]"
                                    aria-hidden="true"
                                />
                            </div>
                            <h3 class="text-lg font-semibold text-[#141b2b]">
                                Assignments &amp; Submissions
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-[#434655]">
                                Create assignments with deadlines, accept file submissions, and
                                release grades with per-student feedback.
                            </p>
                        </article>

                        <article
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 200ms`"
                            class="rounded-2xl bg-white p-8 transition-all duration-200 hover:-translate-y-1 motion-reduce:hover:translate-y-0"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#dbe1ff]"
                            >
                                <ChartBarIcon class="h-6 w-6 text-[#004ac6]" aria-hidden="true" />
                            </div>
                            <h3 class="text-lg font-semibold text-[#141b2b]">Track Progress</h3>
                            <p class="mt-2 text-sm leading-relaxed text-[#434655]">
                                Students and instructors get a real-time dashboard showing grades,
                                submission status, and course completion at a glance.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- How it works -->
            <section class="bg-[#f9f9ff] py-20" aria-labelledby="how-it-works-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2
                        id="how-it-works-heading"
                        class="text-center text-3xl font-bold text-[#141b2b]"
                    >
                        Up and running in minutes
                    </h2>
                    <div class="mt-12 grid gap-8 sm:grid-cols-3">
                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 0ms`"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-[#004ac6] text-lg font-bold text-white"
                            >
                                1
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-[#141b2b]">
                                Create your account
                            </h3>
                            <p class="mt-2 text-sm text-[#434655]">
                                Register with your email and choose your role as a student,
                                instructor, or admin.
                            </p>
                        </div>
                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 100ms`"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-[#004ac6] text-lg font-bold text-white"
                            >
                                2
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-[#141b2b]">
                                Enroll in courses
                            </h3>
                            <p class="mt-2 text-sm text-[#434655]">
                                Browse the course catalog and enroll in your sections for the
                                semester.
                            </p>
                        </div>
                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 200ms`"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-[#004ac6] text-lg font-bold text-white"
                            >
                                3
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-[#141b2b]">
                                Track your progress
                            </h3>
                            <p class="mt-2 text-sm text-[#434655]">
                                Submit assignments, receive grades, and monitor your performance
                                from your personalized dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- For Each Role -->
            <section class="bg-[#f1f3ff] py-20" aria-labelledby="roles-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 id="roles-heading" class="text-center text-3xl font-bold text-[#141b2b]">
                        Built for every role
                    </h2>
                    <div class="mt-12 grid gap-6 sm:grid-cols-3">
                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 0ms`"
                            class="rounded-2xl bg-white p-6 transition-all duration-200 hover:-translate-y-0.5"
                        >
                            <h3
                                class="flex items-center gap-2 text-lg font-semibold text-[#141b2b]"
                            >
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#dbe1ff]"
                                >
                                    <AcademicCapIcon
                                        class="h-4 w-4 text-[#004ac6]"
                                        aria-hidden="true"
                                    />
                                </span>
                                Students
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-[#434655]">
                                <li>Browse and enroll in courses</li>
                                <li>Submit assignments and view grades</li>
                                <li>Access learning materials anytime</li>
                                <li>Receive announcements and notifications</li>
                            </ul>
                        </div>

                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 100ms`"
                            class="rounded-2xl bg-white p-6 transition-all duration-200 hover:-translate-y-0.5"
                        >
                            <h3
                                class="flex items-center gap-2 text-lg font-semibold text-[#141b2b]"
                            >
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#dbe1ff]"
                                >
                                    <UsersIcon class="h-4 w-4 text-[#004ac6]" aria-hidden="true" />
                                </span>
                                Instructors
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-[#434655]">
                                <li>Create and manage course content</li>
                                <li>Design assignments with rubrics</li>
                                <li>Grade submissions and release feedback</li>
                                <li>Post announcements to students</li>
                            </ul>
                        </div>

                        <div
                            v-animateonscroll="{ enterClass: 'animate-fadein' }"
                            :style="`animation-delay: 200ms`"
                            class="rounded-2xl bg-white p-6 transition-all duration-200 hover:-translate-y-0.5"
                        >
                            <h3
                                class="flex items-center gap-2 text-lg font-semibold text-[#141b2b]"
                            >
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#dbe1ff]"
                                >
                                    <Cog6ToothIcon
                                        class="h-4 w-4 text-[#004ac6]"
                                        aria-hidden="true"
                                    />
                                </span>
                                Admins
                            </h3>
                            <ul class="mt-4 space-y-2 text-sm text-[#434655]">
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
                class="bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)] py-16 text-white"
                aria-labelledby="cta-heading"
            >
                <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
                    <h2 id="cta-heading" class="text-3xl font-bold">Ready to get started?</h2>
                    <p class="mt-4 text-lg text-white/80">
                        Join thousands of students and instructors already learning on Eduno.
                    </p>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="mt-8 inline-block rounded-xl bg-white px-8 py-3 text-base font-semibold text-[#004ac6] transition hover:bg-[#f1f3ff] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#004ac6]"
                    >
                        Sign Up for Free
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-[#f1f3ff] py-8 text-center text-sm text-[#434655]">
            <p>© 2026 Eduno LMS</p>
            <p class="mt-1 text-xs text-[#737686]">Empowering Learners, Inspiring Excellence</p>
        </footer>
    </div>
</template>
