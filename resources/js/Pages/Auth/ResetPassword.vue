<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout title="Set a new password" subtitle="Choose a strong password for your account">
        <Head title="Reset Password" />

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
                />

                <InputError
                    id="password_confirmation-error"
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    type="submit"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Resetting&hellip;</span>
                    <span v-else>Reset Password</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
