<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import InputError from '@/Components/InputError.vue';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
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
});

const onFileSelect = (e: FileUploadSelectEvent): void => {
    form.file = e.files[0] ?? null;
};

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
                            <InputText
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
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
                            <FileUpload
                                mode="advanced"
                                accept=".pdf,.docx,.pptx,.xlsx,.mp4,.zip"
                                :multiple="false"
                                :auto="false"
                                class="mt-1"
                                @select="onFileSelect"
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

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <Link
                                :href="route('instructor.courses.modules.index', section.id)"
                                class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                            >
                                Cancel
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                Upload Resource
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
