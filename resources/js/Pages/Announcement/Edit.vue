<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Announcement } from '@/Types/models';

const props = defineProps<{
    announcement: Announcement;
}>();

const form = useForm({
    title: props.announcement.title,
    body: props.announcement.body,
});

function submit(): void {
    form.put(route('announcements.update', props.announcement.id));
}
</script>

<template>
    <Head :title="`Edit — ${announcement.title}`" />

    <main class="mx-auto max-w-2xl px-4 py-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex gap-2 text-sm text-gray-500">
                <li>
                    <Link
                        :href="route('announcements.show', announcement.id)"
                        class="hover:underline"
                    >
                        {{ announcement.title }}
                    </Link>
                </li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Edit</li>
            </ol>
        </nav>

        <h1 class="mb-6 text-2xl font-bold">Edit Announcement</h1>

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
                    <label for="body" class="block text-sm font-medium text-gray-700">
                        Body <span aria-hidden="true">*</span>
                    </label>
                    <textarea
                        id="body"
                        v-model="form.body"
                        rows="8"
                        required
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        :aria-describedby="form.errors.body ? 'body-error' : undefined"
                        :aria-invalid="!!form.errors.body"
                    />
                    <p
                        v-if="form.errors.body"
                        id="body-error"
                        class="mt-1 text-sm text-red-600"
                        role="alert"
                    >
                        {{ form.errors.body }}
                    </p>
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
                    :href="route('announcements.show', announcement.id)"
                    class="rounded border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </main>
</template>
