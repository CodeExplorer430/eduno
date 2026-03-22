<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import type { Course, CourseSection, User } from '@/Types/models';

interface EnrichedSection extends CourseSection {
    instructor: User;
    enrollments: { id: number; user_id: number }[];
}

interface EnrichedCourse extends Course {
    creator: User;
    sections: EnrichedSection[];
}

const props = defineProps<{
    course: EnrichedCourse;
}>();

const page = usePage();
const authUser = page.props.auth.user as User;

const enrollForm = useForm({});

function enroll(sectionId: number): void {
    enrollForm.post(route('sections.enroll', sectionId));
}

function unenroll(sectionId: number): void {
    enrollForm.delete(route('sections.unenroll', sectionId));
}

function isEnrolled(section: EnrichedSection): boolean {
    return section.enrollments.some((e) => e.user_id === authUser.id);
}
</script>

<template>
    <Head :title="`${course.code} — ${course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <nav aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2 text-sm text-gray-500">
                            <li>
                                <Link
                                    :href="route('courses.index')"
                                    class="hover:text-gray-700 focus:underline focus:outline-none"
                                >
                                    Courses
                                </Link>
                            </li>
                            <li aria-hidden="true">/</li>
                            <li class="text-gray-800 font-medium" aria-current="page">
                                {{ course.code }}
                            </li>
                        </ol>
                    </nav>
                    <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-800">
                        {{ course.title }}
                    </h2>
                </div>
                <Link
                    v-if="authUser.role === 'instructor' || authUser.role === 'admin'"
                    :href="route('courses.edit', course.id)"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :aria-label="`Edit course ${course.title}`"
                >
                    Edit Course
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            <div>
                                <dt
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Department
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ course.department }}</dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Term
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ course.term }}</dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Academic Year
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ course.academic_year }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800':
                                                course.status === 'draft',
                                            'bg-green-100 text-green-800':
                                                course.status === 'published',
                                            'bg-gray-100 text-gray-600':
                                                course.status === 'archived',
                                        }"
                                        class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                    >
                                        {{ course.status }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                        <p v-if="course.description" class="mt-4 text-sm text-gray-700">
                            {{ course.description }}
                        </p>
                    </div>
                </div>

                <section aria-labelledby="sections-heading">
                    <h3 id="sections-heading" class="text-lg font-semibold text-gray-800">
                        Sections
                        <span class="text-sm font-normal text-gray-500">(classes / blocks)</span>
                    </h3>
                    <p class="text-sm text-gray-500">
                        Sections correspond to your enrolled class block (e.g., BSCS-2A).
                    </p>

                    <div
                        v-if="course.sections.length === 0"
                        class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6 text-sm text-gray-500">No sections yet.</div>
                    </div>

                    <ul v-else class="mt-4 space-y-3">
                        <li
                            v-for="section in course.sections"
                            :key="section.id"
                            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                        >
                            <div class="flex items-center justify-between p-4">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ section.section_name
                                        }}{{ section.block_code ? ` (${section.block_code})` : '' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Instructor: {{ section.instructor?.name }}
                                        <span v-if="section.schedule_text">
                                            &middot; {{ section.schedule_text }}</span
                                        >
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ section.enrollments?.length ?? 0 }} enrolled
                                    </p>
                                </div>
                                <div v-if="authUser.role === 'student'" class="flex gap-2">
                                    <button
                                        v-if="!isEnrolled(section)"
                                        type="button"
                                        class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        :aria-label="`Enroll in ${section.section_name}`"
                                        :disabled="enrollForm.processing"
                                        @click="enroll(section.id)"
                                    >
                                        Enroll
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="rounded-md border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        :aria-label="`Unenroll from ${section.section_name}`"
                                        :disabled="enrollForm.processing"
                                        @click="unenroll(section.id)"
                                    >
                                        Unenroll
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
