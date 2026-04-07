<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Section {
    id: number;
    section_name: string;
    schedule_text: string | null;
    course: { id: number; code: string; title: string; status: string };
    instructor: { id: number; name: string };
}

defineProps<{
    section: Section;
}>();
</script>

<template>
    <article class="group overflow-hidden rounded-xl bg-white transition hover:shadow-sm">
        <!-- Accent bar -->
        <div class="h-2 bg-[linear-gradient(135deg,#004ac6_0%,#2563eb_100%)]"></div>

        <div class="p-5">
            <header class="mb-3">
                <span
                    class="mb-1 inline-block rounded-full bg-[#dbe1ff] px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-[#00174b]"
                >
                    {{ section.course.code }}
                </span>
                <h2 class="text-base font-semibold text-[#141b2b] leading-snug">
                    {{ section.course.title }}
                </h2>
            </header>

            <dl class="space-y-1.5 text-sm text-[#434655]">
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-[#434655]">Section</dt>
                    <dd class="text-[#141b2b]">{{ section.section_name }}</dd>
                </div>
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-[#434655]">Instructor</dt>
                    <dd class="text-[#141b2b]">{{ section.instructor.name }}</dd>
                </div>
                <div v-if="section.schedule_text" class="flex items-center gap-1.5">
                    <dt class="font-medium text-[#434655]">Schedule</dt>
                    <dd class="text-[#141b2b]">{{ section.schedule_text }}</dd>
                </div>
            </dl>
        </div>

        <footer class="bg-[#f1f3ff] px-5 py-3">
            <Link
                :href="route('student.courses.show', section.id)"
                class="inline-flex items-center gap-1 rounded text-sm font-medium text-[#004ac6] hover:text-[#003ea8] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-1"
                :aria-label="`View ${section.course.code} — ${section.course.title}`"
            >
                View Course
                <svg
                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </Link>
        </footer>
    </article>
</template>
