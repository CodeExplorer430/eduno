<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Course, CourseSection, Lesson, Module, Resource } from '@/Types/models';
import { useFileSize } from '@/composables/useFileSize';

interface LessonWithRelations extends Lesson {
    module: Module & {
        section: CourseSection & { course: Course };
    };
}

const props = defineProps<{
    lesson: LessonWithRelations;
    resources: Resource[];
    canManage: boolean;
}>();

const publishForm = useForm({});
const deleteForm = useForm({});

const uploadForm = useForm({
    title: '',
    file: null as File | null,
    visibility: 'enrolled' as 'enrolled' | 'instructor' | 'public',
});

function togglePublish(): void {
    publishForm.post(route('lessons.publish', props.lesson.id));
}

function destroyResource(resourceId: number): void {
    if (confirm('Delete this resource? The file will be permanently removed.')) {
        deleteForm.delete(route('resources.destroy', resourceId));
    }
}

function handleFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files?.[0]) {
        uploadForm.file = input.files[0];
    }
}

function submitUpload(): void {
    uploadForm.post(route('lessons.resources.store', props.lesson.id), {
        forceFormData: true,
        onSuccess: () => {
            uploadForm.reset();
            const fileInput = document.getElementById('resource-file') as HTMLInputElement | null;
            if (fileInput) fileInput.value = '';
        },
    });
}

const { formatBytes } = useFileSize();
</script>

<template>
    <Head :title="lesson.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start justify-between">
                <div>
                    <Breadcrumb
                        :crumbs="[
                            {
                                label: lesson.module.section.course?.title ?? 'Course',
                                href: route('courses.show', lesson.module.section.course_id),
                            },
                            {
                                label: lesson.module.title,
                                href: route('modules.show', lesson.module_id),
                            },
                            { label: lesson.title },
                        ]"
                    />
                    <div class="mt-1 flex items-center gap-2">
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            {{ lesson.title }}
                        </h2>
                        <span
                            class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium uppercase text-gray-600"
                        >
                            {{ lesson.type }}
                        </span>
                        <span
                            v-if="!lesson.published_at"
                            class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                            role="status"
                        >
                            Draft
                        </span>
                    </div>
                </div>

                <div v-if="canManage" class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :aria-label="lesson.published_at ? 'Unpublish lesson' : 'Publish lesson'"
                        @click="togglePublish"
                    >
                        {{ lesson.published_at ? 'Unpublish' : 'Publish' }}
                    </button>
                    <Link
                        :href="route('lessons.edit', lesson.id)"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        aria-label="Edit lesson"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Lesson content -->
                <div v-if="lesson.content" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <template v-if="lesson.type === 'text'">
                            <div
                                class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line"
                            >
                                {{ lesson.content }}
                            </div>
                        </template>
                        <template v-else-if="lesson.type === 'link'">
                            <a
                                :href="lesson.content"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 focus:underline focus:outline-none"
                                :aria-label="`Open link: ${lesson.content}`"
                            >
                                {{ lesson.content }}
                            </a>
                        </template>
                        <template v-else>
                            <p class="text-sm text-gray-500">
                                {{ lesson.content }}
                            </p>
                        </template>
                    </div>
                </div>

                <!-- Resources -->
                <section aria-labelledby="resources-heading">
                    <h3 id="resources-heading" class="text-lg font-semibold text-gray-800">
                        Resources
                    </h3>

                    <div
                        v-if="resources.length === 0"
                        class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-4 text-sm text-gray-500">
                            No resources attached to this lesson.
                        </div>
                    </div>

                    <ul v-else class="mt-4 space-y-2" aria-label="Resource list">
                        <li
                            v-for="resource in resources"
                            :key="resource.id"
                            class="flex items-center justify-between overflow-hidden bg-white px-4 py-3 shadow-sm sm:rounded-lg"
                        >
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">
                                    {{ resource.title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ resource.mime_type }} &middot;
                                    {{ formatBytes(resource.size_bytes) }}
                                    <span class="ml-1 rounded bg-gray-100 px-1 py-0.5 capitalize">{{
                                        resource.visibility
                                    }}</span>
                                </p>
                            </div>
                            <div class="ml-4 flex shrink-0 gap-2">
                                <a
                                    :href="route('resources.download', resource.id)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="rounded border border-blue-300 bg-white px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :aria-label="`Download ${resource.title}`"
                                >
                                    Download
                                </a>
                                <button
                                    v-if="canManage"
                                    type="button"
                                    class="rounded border border-red-300 bg-white px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    :aria-label="`Delete ${resource.title}`"
                                    @click="destroyResource(resource.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </li>
                    </ul>
                </section>

                <!-- Upload form (instructor only) -->
                <section
                    v-if="canManage"
                    aria-labelledby="upload-heading"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 id="upload-heading" class="mb-4 text-base font-semibold text-gray-800">
                            Upload Resource
                        </h3>

                        <form
                            novalidate
                            enctype="multipart/form-data"
                            @submit.prevent="submitUpload"
                        >
                            <div
                                v-if="uploadForm.hasErrors"
                                role="alert"
                                aria-live="assertive"
                                class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800"
                            >
                                Please fix the errors below.
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        for="resource-title"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Title <span aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="resource-title"
                                        v-model="uploadForm.title"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            uploadForm.errors.title
                                                ? 'resource-title-error'
                                                : undefined
                                        "
                                        :aria-invalid="!!uploadForm.errors.title"
                                    />
                                    <p
                                        v-if="uploadForm.errors.title"
                                        id="resource-title-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ uploadForm.errors.title }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="resource-file"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        File <span aria-hidden="true">*</span>
                                        <span class="ml-1 text-xs text-gray-500">(max 50 MB)</span>
                                    </label>
                                    <input
                                        id="resource-file"
                                        type="file"
                                        required
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mp3,.png,.jpg,.jpeg,.gif"
                                        class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :aria-describedby="
                                            uploadForm.errors.file
                                                ? 'resource-file-error'
                                                : undefined
                                        "
                                        :aria-invalid="!!uploadForm.errors.file"
                                        @change="handleFileChange"
                                    />
                                    <p
                                        v-if="uploadForm.errors.file"
                                        id="resource-file-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ uploadForm.errors.file }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="resource-visibility"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Visibility <span aria-hidden="true">*</span>
                                    </label>
                                    <select
                                        id="resource-visibility"
                                        v-model="uploadForm.visibility"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            uploadForm.errors.visibility
                                                ? 'visibility-error'
                                                : undefined
                                        "
                                        :aria-invalid="!!uploadForm.errors.visibility"
                                    >
                                        <option value="enrolled">Enrolled students</option>
                                        <option value="instructor">Instructor only</option>
                                        <option value="public">Public</option>
                                    </select>
                                    <p
                                        v-if="uploadForm.errors.visibility"
                                        id="visibility-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ uploadForm.errors.visibility }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="uploadForm.processing"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                    :aria-busy="uploadForm.processing"
                                >
                                    {{ uploadForm.processing ? 'Uploading…' : 'Upload Resource' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
