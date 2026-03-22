<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon } from '@heroicons/vue/24/outline';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout
        title="Forgot your password?"
        subtitle="Enter your email and we'll send a reset link"
    >
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600">
            Forgot your password? No problem. Just let us know your email address and we will email
            you a password reset link that will allow you to choose a new one.
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
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
                />

                <InputError id="email-error" class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    type="submit"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Sending&hellip;</span>
                    <span v-else>Email Password Reset Link</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
