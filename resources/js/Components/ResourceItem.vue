<script setup lang="ts">
import { computed } from 'vue';
import { useOfflineCache } from '@/composables/useOfflineCache';
import { ArrowDownTrayIcon, CheckCircleIcon } from '@heroicons/vue/24/solid';

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
            class="truncate text-sm text-blue-600 underline hover:text-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
        >
            {{ filename }}
        </a>

        <div>
            <button
                type="button"
                :aria-label="ariaLabel"
                :disabled="saving || cached"
                class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-md text-gray-500 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:opacity-50"
                @click="saveResource(url)"
            >
                <!-- Saved checkmark -->
                <CheckCircleIcon v-if="cached" class="h-5 w-5 text-green-600" aria-hidden="true" />

                <!-- Download/save icon -->
                <ArrowDownTrayIcon v-else class="h-5 w-5" aria-hidden="true" />
            </button>

            <div role="status" aria-live="polite" class="sr-only">
                <span v-if="cached">{{ filename }} has been saved for offline access.</span>
            </div>
        </div>
    </div>
</template>
