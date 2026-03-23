<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import FileUploadInput from '@/Components/FileUploadInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Section {
    id: number;
    course: { code: string; title: string };
}

interface ModuleData {
    id: number;
    title: string;
}

interface LessonData {
    id: number;
    title: string;
}

const props = defineProps<{
    section: Section;
    module: ModuleData;
    lesson: LessonData;
}>();

const form = useForm({
    title: '',
    file: null as File | null,
    visibility: 'enrolled' as 'enrolled' | 'public',
    accessibility_notes: '' as string,
    reading_time_minutes: null as number | null,
});

const selectedFiles = ref<File[]>([]);
watch(selectedFiles, (files) => {
    form.file = files[0] ?? null;
});

const submit = (): void => {
    form.post(
        route('instructor.courses.modules.lessons.resources.store', {
            section: props.section.id,
            module: props.module.id,
            lesson: props.lesson.id,
        }),
        { forceFormData: true }
    );
};
</script>

<template>
    <Head :title="`Upload Resource — ${section.course.code}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.courses.modules.index', section.id)"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            {{ section.course.code }} — Modules
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Upload Resource</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h1 class="font-semibold text-gray-800">
                            Upload Resource for
                            <span class="text-blue-600">{{ lesson.title }}</span>
                        </h1>
                    </div>

                    <div
                        v-if="form.hasErrors"
                        role="alert"
                        class="border-b border-red-100 bg-red-50 px-6 py-3 text-sm text-red-700"
                    >
                        Please fix the errors below.
                    </div>

                    <form class="space-y-5 px-6 py-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="title" value="Resource Title" />
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-50"
                                :aria-describedby="form.errors.title ? 'title-error' : undefined"
                                required
                                autofocus
                            />
                            <InputError
                                id="title-error"
                                :message="form.errors.title"
                                class="mt-1"
                            />
                        </div>

                        <div>
                            <InputLabel value="File" />
                            <FileUploadInput
                                v-model="selectedFiles"
                                accept=".pdf,.docx,.pptx,.xlsx,.mp4,.zip"
                                :multiple="false"
                                class="mt-1"
                            />
                            <InputError :message="form.errors.file" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="visibility" value="Visibility" />
                            <select
                                id="visibility"
                                v-model="form.visibility"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :aria-describedby="
                                    form.errors.visibility ? 'visibility-error' : undefined
                                "
                                required
                            >
                                <option value="enrolled">Enrolled Students Only</option>
                                <option value="public">Public</option>
                            </select>
                            <InputError
                                id="visibility-error"
                                :message="form.errors.visibility"
                                class="mt-1"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="reading-time"
                                value="Reading Time (minutes, optional)"
                            />
                            <input
                                id="reading-time"
                                v-model.number="form.reading_time_minutes"
                                type="number"
                                min="1"
                                max="999"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-describedby="
                                    form.errors.reading_time_minutes
                                        ? 'reading-time-error'
                                        : undefined
                                "
                                placeholder="e.g. 10"
                            />
                            <InputError
                                id="reading-time-error"
                                :message="form.errors.reading_time_minutes"
                                class="mt-1"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="accessibility-notes"
                                value="Accessibility Notes (optional)"
                            />
                            <textarea
                                id="accessibility-notes"
                                v-model="form.accessibility_notes"
                                rows="3"
                                maxlength="500"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm text-gray-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :aria-describedby="
                                    form.errors.accessibility_notes
                                        ? 'a11y-notes-error'
                                        : 'a11y-notes-hint'
                                "
                                placeholder="e.g. Screen-reader compatible PDF with text layer"
                            />
                            <p id="a11y-notes-hint" class="mt-1 text-xs text-gray-500">
                                Describe any accessibility features or limitations (max 500
                                characters).
                            </p>
                            <InputError
                                id="a11y-notes-error"
                                :message="form.errors.accessibility_notes"
                                class="mt-1"
                            />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <Link
                                :href="route('instructor.courses.modules.index', section.id)"
                                class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Upload Resource
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
