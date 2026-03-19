<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { CourseSection } from '@/Types/models';

const props = defineProps<{
    section: CourseSection;
}>();

const form = useForm({
    title: '',
    description: '',
    order_no: null as number | null,
});

function submit(): void {
    form.post(route('sections.modules.store', props.section.id));
}
</script>

<template>
    <Head title="Create Module" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Module</h2>
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
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                                        for="description"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.description
                                                ? 'description-error'
                                                : undefined
                                        "
                                        :aria-invalid="!!form.errors.description"
                                    />
                                    <p
                                        v-if="form.errors.description"
                                        id="description-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.description }}
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
                                        class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
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
                                    :href="route('sections.modules.index', section.id)"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                    :aria-busy="form.processing"
                                >
                                    {{ form.processing ? 'Creating…' : 'Create Module' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
