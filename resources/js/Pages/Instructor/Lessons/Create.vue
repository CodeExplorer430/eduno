<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
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

const props = defineProps<{
    section: Section;
    module: ModuleData;
}>();

const form = useForm({
    title: '',
    type: 'text' as 'text' | 'video' | 'document' | 'quiz',
    content: '',
    order_no: 0,
    published: false,
});

const submit = (): void => {
    form.post(
        route('instructor.courses.modules.lessons.store', {
            section: props.section.id,
            module: props.module.id,
        })
    );
};
</script>

<template>
    <Head :title="`Add Lesson — ${section.course.code}`" />

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
                    <li class="font-medium text-gray-800" aria-current="page">Add Lesson</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h1 class="font-semibold text-gray-800">
                            Add Lesson to <span class="text-indigo-600">{{ module.title }}</span>
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
                            <InputLabel for="title" value="Title" />
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
                            <InputLabel for="type" value="Lesson Type" />
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :aria-describedby="form.errors.type ? 'type-error' : undefined"
                                required
                            >
                                <option value="text">Text</option>
                                <option value="video">Video</option>
                                <option value="document">Document</option>
                                <option value="quiz">Quiz</option>
                            </select>
                            <InputError id="type-error" :message="form.errors.type" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="content" value="Content (optional)" />
                            <textarea
                                id="content"
                                v-model="form.content"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="6"
                                :aria-describedby="
                                    form.errors.content ? 'content-error' : undefined
                                "
                            />
                            <InputError
                                id="content-error"
                                :message="form.errors.content"
                                class="mt-1"
                            />
                        </div>

                        <div>
                            <InputLabel for="order_no" value="Order" />
                            <input
                                id="order_no"
                                v-model.number="form.order_no"
                                type="number"
                                min="0"
                                class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :aria-describedby="form.errors.order_no ? 'order-error' : undefined"
                                required
                            />
                            <InputError
                                id="order-error"
                                :message="form.errors.order_no"
                                class="mt-1"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="published"
                                v-model="form.published"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <InputLabel for="published" value="Publish immediately" class="mb-0" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <Link
                                :href="route('instructor.courses.modules.index', section.id)"
                                class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :disabled="form.processing">Save Lesson</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
