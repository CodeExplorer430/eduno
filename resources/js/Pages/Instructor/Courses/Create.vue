<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm<{
    code: string;
    title: string;
    description: string;
    department: string;
    term: string;
    academic_year: string;
}>({
    code: '',
    title: '',
    description: '',
    department: '',
    term: '',
    academic_year: '',
});

const submit = (): void => {
    form.post(route('instructor.courses.store'));
};
</script>

<template>
    <Head title="Create Course" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.courses.index')"
                            class="hover:text-gray-700 focus:rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">Create Course</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <main>
                    <section
                        aria-labelledby="create-course-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1 id="create-course-heading" class="text-lg font-bold text-gray-900">
                                Create Course
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">
                                Fill in the details to create a new course.
                            </p>
                        </header>

                        <form novalidate @submit.prevent="submit">
                            <div class="space-y-5 px-6 py-6">
                                <!-- Code -->
                                <div>
                                    <InputLabel for="course-code">
                                        Course Code
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="course-code"
                                        v-model="form.code"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="course-code-error"
                                        :aria-invalid="!!form.errors.code"
                                        required
                                        autofocus
                                    />
                                    <InputError
                                        id="course-code-error"
                                        class="mt-1"
                                        :message="form.errors.code"
                                    />
                                </div>

                                <!-- Title -->
                                <div>
                                    <InputLabel for="course-title">
                                        Course Title
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="course-title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="course-title-error"
                                        :aria-invalid="!!form.errors.title"
                                        required
                                    />
                                    <InputError
                                        id="course-title-error"
                                        class="mt-1"
                                        :message="form.errors.title"
                                    />
                                </div>

                                <!-- Description -->
                                <div>
                                    <InputLabel for="course-description">Description</InputLabel>
                                    <textarea
                                        id="course-description"
                                        v-model="form.description"
                                        rows="4"
                                        aria-describedby="course-description-error"
                                        :aria-invalid="!!form.errors.description"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Optional course description…"
                                    ></textarea>
                                    <InputError
                                        id="course-description-error"
                                        class="mt-1"
                                        :message="form.errors.description"
                                    />
                                </div>

                                <!-- Department -->
                                <div>
                                    <InputLabel for="course-department">
                                        Department
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="course-department"
                                        v-model="form.department"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="course-department-error"
                                        :aria-invalid="!!form.errors.department"
                                        required
                                    />
                                    <InputError
                                        id="course-department-error"
                                        class="mt-1"
                                        :message="form.errors.department"
                                    />
                                </div>

                                <!-- Term -->
                                <div>
                                    <InputLabel for="course-term">
                                        Term
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="course-term"
                                        v-model="form.term"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="course-term-error"
                                        :aria-invalid="!!form.errors.term"
                                        placeholder="e.g. 1st Semester"
                                        required
                                    />
                                    <InputError
                                        id="course-term-error"
                                        class="mt-1"
                                        :message="form.errors.term"
                                    />
                                </div>

                                <!-- Academic Year -->
                                <div>
                                    <InputLabel for="course-academic-year">
                                        Academic Year
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="course-academic-year"
                                        v-model="form.academic_year"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="course-academic-year-error"
                                        :aria-invalid="!!form.errors.academic_year"
                                        placeholder="e.g. 2025–2026"
                                        required
                                    />
                                    <InputError
                                        id="course-academic-year-error"
                                        class="mt-1"
                                        :message="form.errors.academic_year"
                                    />
                                </div>
                            </div>

                            <div
                                v-if="form.hasErrors && Object.keys(form.errors).length > 0"
                                role="alert"
                                aria-live="assertive"
                                class="mx-6 mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700"
                            >
                                Please fix the errors above before submitting.
                            </div>

                            <footer
                                class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                            >
                                <Link :href="route('instructor.courses.index')">
                                    <Button type="button" severity="secondary">Cancel</Button>
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    :aria-busy="form.processing"
                                >
                                    <span v-if="form.processing">Creating&hellip;</span>
                                    <span v-else>Create Course</span>
                                </Button>
                            </footer>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
