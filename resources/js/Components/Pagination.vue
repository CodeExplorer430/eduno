<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginationLink } from '@/Types/models';

defineProps<{ links: PaginationLink[] }>();

function decodeLabel(raw: string): string {
    return raw
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
        .replace(/&amp;/g, '&');
}
</script>

<template>
    <nav aria-label="Pagination" class="flex items-center justify-center gap-1 mt-4">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                :aria-label="decodeLabel(link.label)"
                :aria-current="link.active ? 'page' : undefined"
                class="px-3 py-1 text-sm rounded border"
                :class="
                    link.active
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                "
                >{{ decodeLabel(link.label) }}</Link
            >
            <span v-else class="px-3 py-1 text-sm text-gray-400">{{
                decodeLabel(link.label)
            }}</span>
        </template>
    </nav>
</template>
