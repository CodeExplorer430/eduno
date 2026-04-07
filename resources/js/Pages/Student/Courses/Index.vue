<script setup lang="ts">
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CourseCard from '@/Components/CourseCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head } from '@inertiajs/vue3';
import { BookOpenIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

interface Section {
    id: number;
    section_name: string;
    schedule_text: string | null;
    course: { id: number; code: string; title: string; status: string };
    instructor: { id: number; name: string };
}

const props = defineProps<{
    sections: Section[];
}>();

const search = ref('');
const activeFilter = ref<'all' | 'active' | 'inactive'>('all');

const filtered = computed(() => {
    return props.sections.filter((s) => {
        const matchesSearch =
            !search.value ||
            s.course.title.toLowerCase().includes(search.value.toLowerCase()) ||
            s.course.code.toLowerCase().includes(search.value.toLowerCase()) ||
            s.instructor.name.toLowerCase().includes(search.value.toLowerCase());

        const matchesFilter =
            activeFilter.value === 'all' ||
            (activeFilter.value === 'active' && s.course.status === 'active') ||
            (activeFilter.value === 'inactive' && s.course.status !== 'active');

        return matchesSearch && matchesFilter;
    });
});
</script>

<template>
    <Head title="My Courses" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-lg font-semibold text-[#141b2b]">My Courses</h1>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Search & filter bar -->
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#737686]"
                            aria-hidden="true"
                        />
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Search courses…"
                            aria-label="Search courses"
                            class="w-full rounded-xl bg-[#f1f3ff] py-2.5 pl-9 pr-4 text-sm text-[#141b2b] placeholder-[#737686] transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#004ac6]"
                        />
                    </div>
                    <div class="flex gap-2" role="group" aria-label="Filter courses by status">
                        <button
                            type="button"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                activeFilter === 'all'
                                    ? 'bg-[#dbe1ff] text-[#00174b]'
                                    : 'bg-[#f1f3ff] text-[#434655] hover:bg-[#e1e8fd]',
                            ]"
                            :aria-pressed="activeFilter === 'all'"
                            @click="activeFilter = 'all'"
                        >
                            All
                        </button>
                        <button
                            type="button"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                activeFilter === 'active'
                                    ? 'bg-[#dbe1ff] text-[#00174b]'
                                    : 'bg-[#f1f3ff] text-[#434655] hover:bg-[#e1e8fd]',
                            ]"
                            :aria-pressed="activeFilter === 'active'"
                            @click="activeFilter = 'active'"
                        >
                            Active
                        </button>
                        <button
                            type="button"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                activeFilter === 'inactive'
                                    ? 'bg-[#dbe1ff] text-[#00174b]'
                                    : 'bg-[#f1f3ff] text-[#434655] hover:bg-[#e1e8fd]',
                            ]"
                            :aria-pressed="activeFilter === 'inactive'"
                            @click="activeFilter = 'inactive'"
                        >
                            Inactive
                        </button>
                    </div>
                </div>

                <!-- Course grid -->
                <div
                    v-if="filtered.length > 0"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    role="list"
                    aria-label="Enrolled courses"
                >
                    <div
                        v-for="(section, index) in filtered"
                        :key="section.id"
                        v-animateonscroll="{ enterClass: 'animate-fadein' }"
                        :style="`animation-delay: ${index * 80}ms`"
                        role="listitem"
                    >
                        <CourseCard :section="section" />
                    </div>
                </div>

                <EmptyState
                    v-else-if="sections.length === 0"
                    :icon="BookOpenIcon"
                    title="You are not enrolled in any courses."
                    description="Contact your instructor or administrator to get enrolled."
                />

                <div
                    v-else
                    class="rounded-2xl bg-[#f1f3ff] py-16 text-center"
                    role="status"
                    aria-live="polite"
                >
                    <p class="text-sm text-[#434655]">No courses match your search.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
