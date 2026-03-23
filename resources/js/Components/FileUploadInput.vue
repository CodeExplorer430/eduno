<script setup lang="ts">
import { ref, computed } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';
import { useFileSize } from '@/composables/useFileSize';

const props = defineProps<{
    modelValue: File[];
    accept?: string;
    multiple?: boolean;
}>();

const emit = defineEmits<{
    'update:modelValue': [files: File[]];
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const liveMessage = ref<string>('');

const { formatBytes } = useFileSize();

const inputId = computed<string>(() => 'file-upload-' + Math.random().toString(36).slice(2, 9));

const onFileChange = (event: Event): void => {
    const target = event.target as HTMLInputElement;
    const newFiles = Array.from(target.files ?? []);
    const merged = props.multiple ? [...props.modelValue, ...newFiles] : newFiles;
    emit('update:modelValue', merged);

    if (merged.length === 0) {
        liveMessage.value = 'No files selected.';
    } else if (merged.length === 1) {
        liveMessage.value = `1 file selected: ${merged[0].name}`;
    } else {
        liveMessage.value = `${merged.length} files selected.`;
    }
};

const removeFile = (index: number): void => {
    const updated = props.modelValue.filter((_, i) => i !== index);
    emit('update:modelValue', updated);
    liveMessage.value =
        updated.length === 0
            ? 'All files removed.'
            : `File removed. ${updated.length} file${updated.length !== 1 ? 's' : ''} remaining.`;
    if (inputRef.value) {
        inputRef.value.value = '';
    }
};
</script>

<template>
    <div class="space-y-3">
        <div
            class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-8 transition hover:border-blue-400 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2"
        >
            <label :for="inputId" class="cursor-pointer text-center">
                <span class="block text-sm font-medium text-gray-700">
                    <span class="text-blue-600 underline"
                        >Choose file{{ multiple ? 's' : '' }}</span
                    >
                    or drag and drop
                </span>
                <span class="mt-1 block text-xs text-gray-500">
                    {{ accept ? `Accepted formats: ${accept}` : 'Any file type accepted' }}
                </span>
                <input
                    :id="inputId"
                    ref="inputRef"
                    type="file"
                    class="sr-only"
                    :accept="accept"
                    :multiple="multiple"
                    @change="onFileChange"
                />
            </label>
        </div>

        <!-- Accessible live region for screen-reader announcements -->
        <div aria-live="polite" aria-atomic="true" class="sr-only">
            {{ liveMessage }}
        </div>

        <!-- File list -->
        <ul
            v-if="modelValue.length > 0"
            class="divide-y divide-gray-100 rounded-md border border-gray-200"
        >
            <li
                v-for="(file, index) in modelValue"
                :key="`${file.name}-${index}`"
                class="flex items-center justify-between px-4 py-2 text-sm"
            >
                <span class="truncate text-gray-800">{{ file.name }}</span>
                <span class="ms-3 shrink-0 text-xs text-gray-500">{{
                    formatBytes(file.size)
                }}</span>
                <button
                    type="button"
                    class="ms-4 shrink-0 rounded text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                    :aria-label="`Remove file: ${file.name}`"
                    @click="removeFile(index)"
                >
                    <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                </button>
            </li>
        </ul>
    </div>
</template>
