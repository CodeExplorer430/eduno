<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    code: '',
    title: '',
    description: '',
    department: '',
    term: '',
    academic_year: '',
});

function submit(): void {
    form.post(route('courses.store'));
}
</script>

<template>
    <Head title="Create Course" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Course</h2>
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
                                        for="code"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Course Code <span aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        required
                                        autocomplete="off"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.code ? 'code-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.code"
                                    />
                                    <p
                                        v-if="form.errors.code"
                                        id="code-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.code }}
                                    </p>
                                </div>

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
                                        for="description"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
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
                                        for="department"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Department <span aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="department"
                                        v-model="form.department"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :aria-describedby="
                                            form.errors.department ? 'department-error' : undefined
                                        "
                                        :aria-invalid="!!form.errors.department"
                                    />
                                    <p
                                        v-if="form.errors.department"
                                        id="department-error"
                                        class="mt-1 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {{ form.errors.department }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            for="term"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Term <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="term"
                                            v-model="form.term"
                                            type="text"
                                            required
                                            placeholder="e.g. 1st Semester"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :aria-describedby="
                                                form.errors.term ? 'term-error' : undefined
                                            "
                                            :aria-invalid="!!form.errors.term"
                                        />
                                        <p
                                            v-if="form.errors.term"
                                            id="term-error"
                                            class="mt-1 text-sm text-red-600"
                                            role="alert"
                                        >
                                            {{ form.errors.term }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="academic_year"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Academic Year <span aria-hidden="true">*</span>
                                        </label>
                                        <input
                                            id="academic_year"
                                            v-model="form.academic_year"
                                            type="text"
                                            required
                                            placeholder="e.g. 2025-2026"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :aria-describedby="
                                                form.errors.academic_year
                                                    ? 'academic-year-error'
                                                    : undefined
                                            "
                                            :aria-invalid="!!form.errors.academic_year"
                                        />
                                        <p
                                            v-if="form.errors.academic_year"
                                            id="academic-year-error"
                                            class="mt-1 text-sm text-red-600"
                                            role="alert"
                                        >
                                            {{ form.errors.academic_year }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <a
                                    :href="route('courses.index')"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                    :aria-busy="form.processing"
                                >
                                    {{ form.processing ? 'Creating…' : 'Create Course' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
