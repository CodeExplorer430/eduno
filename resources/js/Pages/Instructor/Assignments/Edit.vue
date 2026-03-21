<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    section: { id: number; section_name: string };
    assignment: {
        id: number;
        title: string;
        instructions: string | null;
        due_at: string | null;
        max_score: number;
        allow_resubmission: boolean;
    };
}

const props = defineProps<Props>();

const form = useForm<{
    title: string;
    instructions: string;
    due_at: string;
    max_score: string;
    allow_resubmission: boolean;
}>({
    title: props.assignment.title,
    instructions: props.assignment.instructions ?? '',
    due_at: props.assignment.due_at
        ? new Date(props.assignment.due_at).toISOString().slice(0, 16)
        : '',
    max_score: String(props.assignment.max_score),
    allow_resubmission: props.assignment.allow_resubmission,
});

const submit = (): void => {
    form.patch(route('instructor.assignments.update', props.assignment.id));
};
</script>

<template>
    <Head title="Edit Assignment" />

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
                    <li class="font-medium text-gray-800" aria-current="page">Edit Assignment</li>
                </ol>
            </nav>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <main>
                    <section
                        aria-labelledby="edit-assignment-heading"
                        class="overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <header class="border-b border-gray-100 px-6 py-4">
                            <h1
                                id="edit-assignment-heading"
                                class="text-lg font-bold text-gray-900"
                            >
                                Edit Assignment
                            </h1>
                            <p class="mt-0.5 text-sm text-gray-500">
                                Section: {{ section.section_name }}
                            </p>
                        </header>

                        <form novalidate @submit.prevent="submit">
                            <div class="space-y-5 px-6 py-6">
                                <div>
                                    <InputLabel for="assignment-title">
                                        Title
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="assignment-title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        aria-describedby="assignment-title-error"
                                        :aria-invalid="!!form.errors.title"
                                        required
                                        autofocus
                                    />
                                    <InputError
                                        id="assignment-title-error"
                                        class="mt-1"
                                        :message="form.errors.title"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="assignment-instructions"
                                        >Instructions</InputLabel
                                    >
                                    <textarea
                                        id="assignment-instructions"
                                        v-model="form.instructions"
                                        rows="5"
                                        aria-describedby="assignment-instructions-error"
                                        :aria-invalid="!!form.errors.instructions"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    ></textarea>
                                    <InputError
                                        id="assignment-instructions-error"
                                        class="mt-1"
                                        :message="form.errors.instructions"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="assignment-due-at">Due Date</InputLabel>
                                    <InputText
                                        id="assignment-due-at"
                                        v-model="form.due_at"
                                        type="datetime-local"
                                        class="mt-1 block w-full"
                                        aria-describedby="assignment-due-at-error"
                                        :aria-invalid="!!form.errors.due_at"
                                    />
                                    <InputError
                                        id="assignment-due-at-error"
                                        class="mt-1"
                                        :message="form.errors.due_at"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="assignment-max-score">
                                        Max Score
                                        <span class="text-red-500" aria-hidden="true">*</span>
                                        <span class="sr-only">(required)</span>
                                    </InputLabel>
                                    <InputText
                                        id="assignment-max-score"
                                        v-model="form.max_score"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="mt-1 block w-32"
                                        aria-describedby="assignment-max-score-error"
                                        :aria-invalid="!!form.errors.max_score"
                                        required
                                    />
                                    <InputError
                                        id="assignment-max-score-error"
                                        class="mt-1"
                                        :message="form.errors.max_score"
                                    />
                                </div>

                                <div class="flex items-center gap-3">
                                    <input
                                        id="assignment-allow-resubmission"
                                        v-model="form.allow_resubmission"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label
                                        for="assignment-allow-resubmission"
                                        class="text-sm text-gray-700"
                                    >
                                        Allow resubmission
                                    </label>
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
                                    <span v-if="form.processing">Saving&hellip;</span>
                                    <span v-else>Save Changes</span>
                                </Button>
                            </footer>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
