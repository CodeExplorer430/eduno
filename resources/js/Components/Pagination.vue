<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginationLink } from '@/Types/models';

defineProps<{ links: PaginationLink[] }>();
</script>

<template>
    <nav aria-label="Pagination" class="flex items-center justify-center gap-1 mt-4">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                :aria-label="link.label"
                :aria-current="link.active ? 'page' : undefined"
                class="px-3 py-1 text-sm rounded border"
                :class="
                    link.active
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                "
                v-html="link.label"
            />
            <span v-else class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
        </template>
    </nav>
</template>
