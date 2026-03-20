<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    Course,
} from '@/types';
import Heading from '@/components/Heading.vue';
import Discussion from '@/components/discussion/Discussion.vue';

const props = defineProps<{
    course: Course;
    courses?: Course[];
    threads?: any[];
    filters?: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kursus',
        href: '/courses',
    },
    {
        title: props.course.title,
        href: `/courses/${props.course.id}`,
    },
    {
        title: 'Forum Diskusi',
        href: '#',
    },
];
</script>

<template>

    <Head :title="`Forum Diskusi - ${course.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <Heading :title="`Forum Diskusi - ${course.title}`"
                description="Kelola dan pantau diskusi dari setiap materi pada kursus ini." />

            <!-- Search + Filter Row -->
            <Discussion :course="course" :courses="courses" :threads="threads" :filters="filters" />
        </div>
    </AppLayout>
</template>
