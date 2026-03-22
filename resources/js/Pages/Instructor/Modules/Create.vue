<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import InputError from '@/Components/InputError.vue';
import Button from 'primevue/button';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Section {
    id: number;
    section_name: string;
    course: { code: string; title: string };
}

const props = defineProps<{
    section: Section;
}>();

const form = useForm({
    title: '',
    description: '',
    order_no: 0,
    published: false,
});

const submit = (): void => {
    form.post(route('instructor.courses.modules.store', props.section.id));
};
</script>

<template>
    <Head :title="`Add Module — ${section.course.code}`" />

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
                    <li class="font-medium text-gray-800" aria-current="page">Add Module</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h1 class="font-semibold text-gray-800">Add Module</h1>
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
                            <InputLabel for="description" value="Description (optional)" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                rows="3"
                                :aria-describedby="
                                    form.errors.description ? 'description-error' : undefined
                                "
                            />
                            <InputError
                                id="description-error"
                                :message="form.errors.description"
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
                                class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <InputLabel for="published" value="Publish immediately" class="mb-0" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <Link
                                :href="route('instructor.courses.modules.index', section.id)"
                                class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                            >
                                Cancel
                            </Link>
                            <Button type="submit" :disabled="form.processing">Save Module</Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
