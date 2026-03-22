<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import IconInput from '@/Components/IconInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LockClosedIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <GuestLayout title="Confirm your password" subtitle="Re-enter your password to continue">
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your password before
            continuing.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Password" />

                <IconInput
                    id="password"
                    v-model="form.password"
                    :icon="LockClosedIcon"
                    type="password"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    type="submit"
                    class="ms-4"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                >
                    <span v-if="form.processing">Confirming&hellip;</span>
                    <span v-else>Confirm</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
