<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FileUploadInput from '@/Components/FileUploadInput.vue';
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

const files = ref<File[]>([]);

const form = useForm({
    title: '',
    file: null as File | null,
    visibility: 'enrolled' as 'enrolled' | 'public',
});

const submit = (): void => {
    if (files.value.length > 0) {
        form.file = files.value[0];
    }
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
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                            <span class="text-indigo-600">{{ lesson.title }}</span>
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
                            <TextInput
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
                            <InputLabel for="file-upload-input" value="File" />
                            <FileUploadInput
                                v-model="files"
                                accept=".pdf,.docx,.pptx,.xlsx,.mp4,.zip"
                                class="mt-1"
                            />
                            <InputError :message="form.errors.file" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="visibility" value="Visibility" />
                            <select
                                id="visibility"
                                v-model="form.visibility"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :disabled="form.processing"
                                >Upload Resource</PrimaryButton
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
