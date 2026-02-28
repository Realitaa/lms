<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import CourseForm from '@/components/courses/CourseForm.vue';
import TitleWithBack from '@/components/TitleWithBack.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/courses';
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
  {
    title: 'Edit Kursus',
    href: '#',
  },
];

const isSubmitting = ref(false);

function onSubmit(formData: FormData) {
  formData.append('_method', 'PUT');
  isSubmitting.value = true;
  router.post(update.url(props.course.id), formData, {
    onSuccess: () => {
      toast.success('Kursus berhasil diperbarui');
    },
    onError: (errors) => {
      toast.error(`Gagal memperbarui kursus: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
}
</script>

<template>

  <Head title="Edit Kursus" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <TitleWithBack
        back-url="/courses"
        :title="`Edit Kursus ${course.title}`"
        :subtitle="`Ubah detail kursus ${course.title}`"
      />

      <CourseForm :course="course" :is-submitting="isSubmitting" @submit="onSubmit" />
    </div>
  </AppLayout>
</template>
