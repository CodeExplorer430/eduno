<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Course {
    id: number;
    code: string;
    title: string;
    description: string | null;
    department: string;
    term: string;
    academic_year: string;
    status: string;
}

const props = defineProps<{
    course: Course;
}>();

const form = useForm<{
    code: string;
    title: string;
    description: string;
    department: string;
    term: string;
    academic_year: string;
}>({
    code: props.course.code,
    title: props.course.title,
    description: props.course.description ?? '',
    department: props.course.department,
    term: props.course.term,
    academic_year: props.course.academic_year,
});

const submit = (): void => {
    form.patch(route('instructor.courses.update', props.course.id));
};
</script>

<template>
    <Head :title="`Edit — ${course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li>
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Courses
                        </Link>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-gray-800" aria-current="page">
                        Edit {{ course.title }}
                    </li>
                </ol>
            </nav>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <section
                aria-labelledby="edit-course-heading"
                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"
            >
                <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                    <div class="h-4 w-1 rounded-full bg-blue-500" aria-hidden="true"></div>
                    <div>
                        <h1 id="edit-course-heading" class="font-semibold text-gray-900">
                            Edit Course
                        </h1>
                        <p class="text-xs text-gray-500">{{ course.title }}</p>
                    </div>
                </div>

                <form novalidate @submit.prevent="submit">
                    <div class="grid gap-6 px-6 py-6 sm:grid-cols-2">
                        <!-- Left: main fields -->
                        <div class="space-y-5">
                            <div>
                                <InputLabel for="course-code">
                                    Course Code
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="course-code"
                                    v-model="form.code"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
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

                            <div>
                                <InputLabel for="course-title">
                                    Course Title
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="course-title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
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

                            <div>
                                <InputLabel for="course-description">Description</InputLabel>
                                <textarea
                                    id="course-description"
                                    v-model="form.description"
                                    rows="4"
                                    aria-describedby="course-description-error"
                                    :aria-invalid="!!form.errors.description"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                ></textarea>
                                <InputError
                                    id="course-description-error"
                                    class="mt-1"
                                    :message="form.errors.description"
                                />
                            </div>
                        </div>

                        <!-- Right: metadata -->
                        <div class="space-y-5">
                            <div>
                                <InputLabel for="course-department">
                                    Department
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="course-department"
                                    v-model="form.department"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
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

                            <div>
                                <InputLabel for="course-term">
                                    Term
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="course-term"
                                    v-model="form.term"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    aria-describedby="course-term-error"
                                    :aria-invalid="!!form.errors.term"
                                    required
                                />
                                <InputError
                                    id="course-term-error"
                                    class="mt-1"
                                    :message="form.errors.term"
                                />
                            </div>

                            <div>
                                <InputLabel for="course-academic-year">
                                    Academic Year
                                    <span class="text-red-500" aria-hidden="true">*</span>
                                    <span class="sr-only">(required)</span>
                                </InputLabel>
                                <input
                                    id="course-academic-year"
                                    v-model="form.academic_year"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    aria-describedby="course-academic-year-error"
                                    :aria-invalid="!!form.errors.academic_year"
                                    required
                                />
                                <InputError
                                    id="course-academic-year-error"
                                    class="mt-1"
                                    :message="form.errors.academic_year"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="form.wasSuccessful"
                        role="status"
                        aria-live="polite"
                        class="mx-6 mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700"
                    >
                        Course updated successfully.
                    </div>

                    <div
                        v-if="form.hasErrors && Object.keys(form.errors).length > 0"
                        role="alert"
                        aria-live="assertive"
                        class="mx-6 mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        Please fix the errors above before submitting.
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                    >
                        <Link
                            :href="route('instructor.courses.index')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            :aria-busy="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60"
                        >
                            <span v-if="form.processing">Saving&hellip;</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
