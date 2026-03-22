<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Module } from '@/Types/models';

const props = defineProps<{
    module: Module;
}>();

const form = useForm({
    title: '',
    content: '',
    type: 'text' as 'text' | 'pdf' | 'video' | 'link',
    order_no: null as number | null,
});

function submit(): void {
    form.post(route('modules.lessons.store', props.module.id));
}
</script>

<template>
    <Head title="Create Lesson" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Lesson</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form novalidate @submit.prevent="submit">
                            <div
                                v-if="form.hasErrors"
                                role="alert"
                                aria-live="assertive"
                                class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800"
                            >
                                Please fix the errors below.
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        for="title"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Title <span aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.title ? 'title-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.title"
                                    />
                                    <p
                                        v-if="form.errors.title"
                                        id="title-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="type"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Type <span aria-hidden="true">*</span>
                                    </label>
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.type ? 'type-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.type"
                                    >
                                        <option value="text">Text</option>
                                        <option value="pdf">PDF</option>
                                        <option value="video">Video</option>
                                        <option value="link">Link</option>
                                    </select>
                                    <p
                                        v-if="form.errors.type"
                                        id="type-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.type }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="content"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Content
                                        <span
                                            v-if="form.type === 'link'"
                                            class="text-xs text-gray-500"
                                            >(URL)</span
                                        >
                                    </label>
                                    <textarea
                                        v-if="form.type === 'text'"
                                        id="content"
                                        v-model="form.content"
                                        rows="6"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.content ? 'content-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.content"
                                    />
                                    <input
                                        v-else
                                        id="content"
                                        v-model="form.content"
                                        type="text"
                                        :placeholder="
                                            form.type === 'link' ? 'https://...' : 'Optional notes'
                                        "
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.content ? 'content-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.content"
                                    />
                                    <p
                                        v-if="form.errors.content"
                                        id="content-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.content }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        for="order_no"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Order (optional)
                                    </label>
                                    <input
                                        id="order_no"
                                        v-model.number="form.order_no"
                                        type="number"
                                        min="0"
                                        class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.order_no ? 'order-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.order_no"
                                    />
                                    <p
                                        v-if="form.errors.order_no"
                                        id="order-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.order_no }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <Link
                                    :href="route('modules.show', module.id)"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                    :aria-busy="form.processing"
                                >
                                    {{ form.processing ? 'Creating…' : 'Create Lesson' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
