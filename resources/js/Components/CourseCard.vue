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
    <article
        class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100 transition hover:shadow-md"
    >
        <!-- Accent bar -->
        <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-400"></div>

        <div class="p-5">
            <header class="mb-3">
                <span
                    class="mb-1 inline-block rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-blue-600"
                >
                    {{ section.course.code }}
                </span>
                <h2 class="text-base font-semibold text-gray-900 leading-snug">
                    {{ section.course.title }}
                </h2>
            </header>

            <dl class="space-y-1.5 text-sm text-gray-600">
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-gray-500">Section</dt>
                    <dd class="text-gray-700">{{ section.section_name }}</dd>
                </div>
                <div class="flex items-center gap-1.5">
                    <dt class="font-medium text-gray-500">Instructor</dt>
                    <dd class="text-gray-700">{{ section.instructor.name }}</dd>
                </div>
                <div v-if="section.schedule_text" class="flex items-center gap-1.5">
                    <dt class="font-medium text-gray-500">Schedule</dt>
                    <dd class="text-gray-700">{{ section.schedule_text }}</dd>
                </div>
            </dl>
        </div>

        <footer class="border-t border-gray-100 px-5 py-3">
            <Link
                :href="route('student.courses.show', section.id)"
                class="inline-flex items-center gap-1 rounded text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
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
