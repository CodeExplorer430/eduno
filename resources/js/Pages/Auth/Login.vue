<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout title="Welcome back" subtitle="Sign in to your Eduno account">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div
            v-if="form.hasErrors && !form.errors.email && !form.errors.password"
            role="alert"
            class="mb-4 rounded-md bg-[#ba1a1a]/10 p-4 text-sm text-[#ba1a1a]"
        >
            An error occurred. Please try again.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <IconInput
                    id="email"
                    v-model="form.email"
                    :icon="EnvelopeIcon"
                    type="email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                    aria-describedby="password-error"
                    :aria-invalid="!!form.errors.password"
                />

                <InputError id="password-error" class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center gap-2">
                    <input
                        id="remember-me"
                        v-model="form.remember"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-[#c3c6d7] text-[#004ac6] focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-2"
                    />
                    <span class="text-sm text-[#434655]">Remember me</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-[#434655] underline hover:text-[#141b2b] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    type="submit"
                    class="ms-4"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Logging in&hellip;</span>
                    <span v-else>Log in</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- OAuth divider -->
        <div class="mt-6 flex items-center gap-3" aria-hidden="true">
            <div class="h-px flex-1 bg-[#c3c6d7]"></div>
            <span class="text-xs text-[#737686] select-none">or</span>
            <div class="h-px flex-1 bg-[#c3c6d7]"></div>
        </div>

        <!-- Google SSO -->
        <a
            :href="route('auth.google.redirect')"
            aria-label="Sign in with Google"
            class="mt-4 flex w-full items-center justify-center gap-3 rounded-lg bg-[#f1f3ff] px-4 py-2.5 text-sm font-medium text-[#141b2b] transition hover:bg-[#e1e8fd] focus:outline-none focus:ring-2 focus:ring-[#004ac6] focus:ring-offset-2"
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
