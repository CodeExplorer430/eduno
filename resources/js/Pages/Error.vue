<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    status: number;
}>();

const title = computed(() => {
    return (
        {
            403: 'Access Denied',
            404: 'Page Not Found',
            500: 'Server Error',
        }[props.status] ?? 'An Error Occurred'
    );
});

const description = computed(() => {
    return (
        {
            403: 'You do not have permission to access this page.',
            404: 'The page you are looking for could not be found.',
            500: 'Something went wrong on our end. Please try again later.',
        }[props.status] ?? 'An unexpected error occurred.'
    );
});
</script>

<template>
    <main
        class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 py-16 text-center"
        role="main"
    >
        <p class="text-8xl font-extrabold text-indigo-600" aria-hidden="true">
            {{ status }}
        </p>

        <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">
            {{ title }}
        </h1>

        <p class="mt-4 text-base text-gray-600">
            {{ description }}
        </p>

        <div class="mt-8">
            <Link
                href="/"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                Go back home
            </Link>
        </div>
    </main>
</template>
