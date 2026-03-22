<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import Button from 'primevue/button';
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
    <GuestLayout>
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

                <Button
                    type="submit"
                    class="ms-4"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Registering&hellip;</span>
                    <span v-else>Register</span>
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
