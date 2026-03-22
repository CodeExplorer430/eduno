<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/vue3';

interface Props {
    submissionId: number;
    maxScore: number;
    existingGrade?: { score: number; feedback: string | null } | null;
}

const props = defineProps<Props>();

const form = useForm<{
    score: string;
    feedback: string;
}>({
    score: props.existingGrade ? String(props.existingGrade.score) : '',
    feedback: props.existingGrade?.feedback ?? '',
});

const submit = (): void => {
    form.post(route('instructor.grades.store', { submission: props.submissionId }));
};
</script>

<template>
    <section aria-labelledby="grade-form-heading">
        <header class="mb-4">
            <h2 id="grade-form-heading" class="text-base font-semibold text-gray-900">
                {{ existingGrade ? 'Update Grade' : 'Grade Submission' }}
            </h2>
        </header>

        <form novalidate @submit.prevent="submit">
            <div class="space-y-4">
                <div>
                    <InputLabel for="grade-score">
                        Score
                        <span class="text-red-500" aria-hidden="true">*</span>
                        <span class="sr-only">(required)</span>
                    </InputLabel>
                    <div class="mt-1 flex items-center gap-2">
                        <InputText
                            id="grade-score"
                            v-model="form.score"
                            type="number"
                            :min="0"
                            :max="maxScore"
                            step="0.01"
                            class="w-32"
                            aria-describedby="grade-score-error"
                            :aria-invalid="!!form.errors.score"
                            required
                        />
                        <span class="text-sm text-gray-500">/ {{ maxScore }} pts</span>
                    </div>
                    <InputError id="grade-score-error" class="mt-1" :message="form.errors.score" />
                </div>

                <div>
                    <InputLabel for="grade-feedback">Feedback</InputLabel>
                    <textarea
                        id="grade-feedback"
                        v-model="form.feedback"
                        rows="4"
                        aria-describedby="grade-feedback-error"
                        :aria-invalid="!!form.errors.feedback"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Optional feedback for the student…"
                    ></textarea>
                    <InputError
                        id="grade-feedback-error"
                        class="mt-1"
                        :message="form.errors.feedback"
                    />
                </div>
            </div>

            <div
                v-if="form.wasSuccessful"
                role="status"
                aria-live="polite"
                class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                Grade saved successfully.
            </div>

            <div
                v-if="form.hasErrors && !form.errors.score && !form.errors.feedback"
                role="alert"
                aria-live="assertive"
                class="mt-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                There was a problem saving the grade. Please check the fields above.
            </div>

            <div class="mt-4 flex justify-end">
                <Button type="submit" :disabled="form.processing" :aria-busy="form.processing">
                    <span v-if="form.processing">Saving&hellip;</span>
                    <span v-else>{{ existingGrade ? 'Update Grade' : 'Save Grade' }}</span>
                </Button>
            </div>
        </form>
    </section>
</template>
