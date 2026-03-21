<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface ResourceItem {
    id: number;
    title: string;
    mime_type: string;
    size_bytes: number;
}

interface LessonDetail {
    id: number;
    title: string;
    content: string | null;
    type: string;
    published_at: string;
    module: {
        title: string;
        course_section: {
            id: number;
            section_name: string;
            course: { code: string; title: string };
        };
    };
    resources: ResourceItem[];
}

defineProps<{
    lesson: LessonDetail;
}>();

const formatBytes = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};
</script>

<template>
    <Head :title="lesson.title" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('student.courses.show', lesson.module.course_section.id)"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            {{ lesson.module.course_section.course.code }}
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="text-gray-500">{{ lesson.module.title }}</li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        {{ lesson.title }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <main class="space-y-6">
                    <!-- Lesson content -->
                    <article
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                        aria-labelledby="lesson-title"
                    >
                        <header class="border-b border-gray-100 bg-indigo-50 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700"
                                >
                                    {{ lesson.type }}
                                </span>
                            </div>
                            <h1 id="lesson-title" class="mt-1 text-xl font-bold text-gray-900">
                                {{ lesson.title }}
                            </h1>
                        </header>

                        <div class="px-6 py-6">
                            <div
                                v-if="lesson.content"
                                class="prose prose-sm max-w-none whitespace-pre-wrap text-gray-700"
                            >
                                {{ lesson.content }}
                            </div>
                            <p v-else class="text-sm text-gray-400">
                                No written content for this lesson.
                            </p>
                        </div>
                    </article>

                    <!-- Resources -->
                    <section
                        v-if="lesson.resources.length > 0"
                        aria-labelledby="resources-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h2 id="resources-heading" class="font-semibold text-gray-800">
                                Downloads
                            </h2>
                        </div>

                        <ul class="divide-y divide-gray-100" aria-label="Lesson resources">
                            <li
                                v-for="resource in lesson.resources"
                                :key="resource.id"
                                class="flex items-center justify-between px-6 py-3"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ resource.title }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ resource.mime_type }} &bull;
                                        {{ formatBytes(resource.size_bytes) }}
                                    </p>
                                </div>
                                <a
                                    :href="route('student.resources.download', resource.id)"
                                    class="rounded text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :aria-label="`Download ${resource.title}`"
                                >
                                    Download
                                </a>
                            </li>
                        </ul>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
