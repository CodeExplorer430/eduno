<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
};

const focusPasswordInput = (): void => {
    window.document.getElementById('delete-password')?.focus();
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => focusPasswordInput(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently
                deleted. Before deleting your account, please download any data or information that
                you wish to retain.
            </p>
        </header>

        <Button severity="danger" @click="confirmUserDeletion">Delete Account</Button>

        <Dialog
            v-model:visible="confirmingUserDeletion"
            :closable="true"
            modal
            header="Delete Account"
            @show="focusPasswordInput"
            @hide="closeModal"
        >
            <p class="mb-4 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently
                deleted. Please enter your password to confirm you would like to permanently delete
                your account.
            </p>

            <div class="mt-2">
                <InputLabel for="delete-password" value="Password" class="sr-only" />

                <InputText
                    id="delete-password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                    aria-describedby="delete-password-error"
                    @keyup.enter="deleteUser"
                />

                <InputError
                    id="delete-password-error"
                    :message="form.errors.password"
                    class="mt-2"
                />
            </div>

            <template #footer>
                <Button severity="secondary" @click="closeModal">Cancel</Button>
                <Button
                    severity="danger"
                    :disabled="form.processing"
                    :aria-busy="form.processing"
                    @click="deleteUser"
                >
                    <span v-if="form.processing">Deleting&hellip;</span>
                    <span v-else>Delete Account</span>
                </Button>
            </template>
        </Dialog>
    </section>
</template>
