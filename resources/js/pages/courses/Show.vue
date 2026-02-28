<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import TitleWithBack from '@/components/TitleWithBack.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Course } from '@/types';

const props = defineProps<{
  course: Course;
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
];
</script>

<template>

  <Head title="Kursus" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <TitleWithBack :title="course.title" :subtitle="`Lihat statistik lengkap kursus ${course.title}`" :backUrl="`/courses`" />
      <div class="flex justify-between p-6">
        <div>
          <h2>{{ course.title }}</h2>
          <p>{{ course.description }}</p>
          <p>{{ course.code }}</p>
        </div>
        <div class="relative aspect-video w-64">
          <img :src="`/storage/${course.cover_image}`" alt="Course Image" class="absolute inset-0 h-full w-full rounded-lg object-cover" />
        </div>
      </div>
      <div class="rounded-lg border bg-card p-6">
        <p class="text-center text-muted-foreground">Fitur statistik kursus sedang dalam pengembangan.</p>
      </div>
    </div>
  </AppLayout>
</template>
