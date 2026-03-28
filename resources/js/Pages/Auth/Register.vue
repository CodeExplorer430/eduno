<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserIcon, EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout title="Create your account" subtitle="Start learning with Eduno today">
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <IconInput
                    id="name"
                    v-model="form.name"
                    :icon="UserIcon"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    aria-describedby="name-error"
                    :aria-invalid="!!form.errors.name"
                />

                <InputError id="name-error" class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <IconInput
                    id="email"
                    v-model="form.email"
                    :icon="EnvelopeIcon"
                    type="email"
                    required
                    autocomplete="username"
                    aria-describedby="email-error"
                    :aria-invalid="!!form.errors.email"
                />

                <InputError id="email-error" class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <IconInput
                    id="password"
                    v-model="form.password"
                    :icon="LockClosedIcon"
                    type="password"
                    required
                    autocomplete="new-password"
                    aria-describedby="password-error"
                    :aria-invalid="!!form.errors.password"
                />

                <InputError id="password-error" class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <IconInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :icon="LockClosedIcon"
                    type="password"
                    required
                    autocomplete="new-password"
                    aria-describedby="password_confirmation-error"
                    :aria-invalid="!!form.errors.password_confirmation"
                />

                <InputError
                    id="password_confirmation-error"
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    type="submit"
                    class="ms-4"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Registering&hellip;</span>
                    <span v-else>Register</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- OAuth divider -->
        <div class="mt-6 flex items-center gap-3" aria-hidden="true">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-xs text-gray-400 select-none">or</span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        <!-- Google SSO -->
        <a
            :href="route('auth.google.redirect')"
            aria-label="Sign up with Google"
            class="mt-4 flex w-full items-center justify-center gap-3 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            <!-- Google "G" logo (official brand SVG) -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 48 48"
                class="h-5 w-5 shrink-0"
                aria-hidden="true"
            >
                <path
                    fill="#EA4335"
                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"
                />
                <path
                    fill="#4285F4"
                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"
                />
                <path
                    fill="#FBBC05"
                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"
                />
                <path
                    fill="#34A853"
                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"
                />
            </svg>
            Continue with Google
        </a>
    </GuestLayout>
</template>
