<script setup lang="ts">
import { computed } from 'vue';
import { useOfflineCache } from '@/composables/useOfflineCache';

const props = defineProps<{
    url: string;
    filename: string;
}>();

const { saving, isCached, saveResource } = useOfflineCache();
const cached = isCached(props.url);

const ariaLabel = computed(() =>
    cached.value
        ? `${props.filename} saved for offline access`
        : `Save ${props.filename} for offline access`
);
</script>

<template>
    <div class="flex items-center justify-between gap-4 py-2">
        <a
            :href="url"
            target="_blank"
            rel="noopener noreferrer"
            class="truncate text-sm text-indigo-600 underline hover:text-indigo-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
        >
            {{ filename }}
        </a>

        <div>
            <button
                type="button"
                :aria-label="ariaLabel"
                :disabled="saving || cached"
                class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-md text-gray-500 hover:text-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-50"
                @click="saveResource(url)"
            >
                <!-- Saved checkmark -->
                <svg
                    v-if="cached"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-600"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>

                <!-- Download/save icon -->
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <div role="status" aria-live="polite" class="sr-only">
                <span v-if="cached">{{ filename }} has been saved for offline access.</span>
            </div>
        </div>
    </div>
</template>
