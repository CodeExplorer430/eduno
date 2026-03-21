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
        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
    >
        <div class="p-5">
            <header class="mb-3">
                <span
                    class="mb-1 block text-xs font-semibold uppercase tracking-wide text-indigo-600"
                >
                    {{ section.course.code }}
                </span>
                <h2 class="text-base font-semibold text-gray-900">
                    {{ section.course.title }}
                </h2>
            </header>

            <dl class="space-y-1 text-sm text-gray-600">
                <div class="flex items-center gap-1">
                    <dt class="font-medium text-gray-700">Section:</dt>
                    <dd>{{ section.section_name }}</dd>
                </div>
                <div class="flex items-center gap-1">
                    <dt class="font-medium text-gray-700">Instructor:</dt>
                    <dd>{{ section.instructor.name }}</dd>
                </div>
                <div v-if="section.schedule_text" class="flex items-center gap-1">
                    <dt class="font-medium text-gray-700">Schedule:</dt>
                    <dd>{{ section.schedule_text }}</dd>
                </div>
            </dl>
        </div>

        <footer class="border-t border-gray-100 bg-gray-50 px-5 py-3">
            <Link
                :href="route('student.courses.show', section.id)"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded"
                :aria-label="`View ${section.course.code} — ${section.course.title}`"
            >
                View Course &rarr;
            </Link>
        </footer>
    </article>
</template>
