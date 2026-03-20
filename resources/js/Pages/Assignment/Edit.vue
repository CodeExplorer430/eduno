<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Assignment } from '@/Types/models';

const props = defineProps<{
    assignment: Assignment;
}>();

const form = useForm({
    title: props.assignment.title,
    instructions: props.assignment.instructions ?? '',
    due_at: props.assignment.due_at ? props.assignment.due_at.slice(0, 16) : '',
    max_score: props.assignment.max_score as number | string,
    allow_resubmission: props.assignment.allow_resubmission,
});

const isPastDue = computed(() => !!form.due_at && new Date(form.due_at) < new Date());

function submit(): void {
    form.put(route('assignments.update', props.assignment.id));
}
</script>

<template>
    <Head :title="`Edit — ${assignment.title}`" />

    <main class="mx-auto max-w-2xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
                <li>
                    <Link :href="route('assignments.show', assignment.id)" class="hover:underline">
                        {{ assignment.title }}
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Edit</li>
            </ol>
        </nav>

        <h1 class="mb-6 text-2xl font-bold">Edit Assignment</h1>

        <form novalidate @submit.prevent="submit">
            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Title <span aria-hidden="true">*</span>
                    </label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        required
                        autocomplete="off"
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        :aria-describedby="form.errors.title ? 'title-error' : undefined"
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
                    <label for="instructions" class="block text-sm font-medium text-gray-700"
                        >Instructions</label
                    >
                    <textarea
                        id="instructions"
                        v-model="form.instructions"
                        rows="6"
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        :aria-describedby="
                            form.errors.instructions ? 'instructions-error' : undefined
                        "
                        :aria-invalid="!!form.errors.instructions"
                    />
                    <p
                        v-if="form.errors.instructions"
                        id="instructions-error"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.instructions }}
                    </p>
                </div>

                <div>
                    <label for="due_at" class="block text-sm font-medium text-gray-700"
                        >Due Date</label
                    >
                    <input
                        id="due_at"
                        v-model="form.due_at"
                        type="datetime-local"
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        :aria-describedby="form.errors.due_at ? 'due-at-error' : undefined"
                        :aria-invalid="!!form.errors.due_at"
                    />
                    <p
                        v-if="form.errors.due_at"
                        id="due-at-error"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.due_at }}
                    </p>
                    <p v-if="isPastDue" class="mt-1 text-sm text-amber-600" role="alert">
                        This due date is in the past. Students will not be able to submit.
                    </p>
                </div>

                <div>
                    <label for="max_score" class="block text-sm font-medium text-gray-700"
                        >Max Score</label
                    >
                    <input
                        id="max_score"
                        v-model="form.max_score"
                        type="number"
                        min="1"
                        max="9999"
                        step="0.01"
                        class="mt-1 block w-40 rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        :aria-describedby="form.errors.max_score ? 'max-score-error' : undefined"
                        :aria-invalid="!!form.errors.max_score"
                    />
                    <p
                        v-if="form.errors.max_score"
                        id="max-score-error"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.max_score }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <input
                        id="allow_resubmission"
                        v-model="form.allow_resubmission"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    />
                    <label for="allow_resubmission" class="text-sm font-medium text-gray-700">
                        Allow resubmission
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-60"
                >
                    Save Changes
                </button>
                <Link
                    :href="route('assignments.show', assignment.id)"
                    class="rounded border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </main>
</template>
