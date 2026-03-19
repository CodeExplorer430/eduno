<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Crumb {
    label: string;
    href?: string;
}

defineProps<{ crumbs: Crumb[] }>();
</script>

<template>
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <template v-for="(crumb, index) in crumbs" :key="index">
                <li v-if="index < crumbs.length - 1">
                    <Link
                        :href="crumb.href ?? '#'"
                        class="hover:text-gray-700 focus:underline focus:outline-none"
                    >
                        {{ crumb.label }}
                    </Link>
                </li>
                <li v-else class="font-medium text-gray-800" aria-current="page">
                    {{ crumb.label }}
                </li>
                <li v-if="index < crumbs.length - 1" aria-hidden="true">/</li>
            </template>
        </ol>
    </nav>
</template>
