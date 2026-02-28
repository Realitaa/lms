<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import CourseForm from '@/components/courses/CourseForm.vue';
import { Button } from '@/components/ui/button';
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
      <div class="flex items-center gap-4">
        <Button variant="outline" size="icon" as-child>
          <Link href="/courses">
            <ArrowLeft class="h-4 w-4" />
          </Link>
        </Button>
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Edit Kursus</h1>
          <p class="text-muted-foreground">Ubah detail kursus <strong>{{ course.title }}</strong></p>
        </div>
      </div>

      <CourseForm :course="course" :is-submitting="isSubmitting" @submit="onSubmit" />
    </div>
  </AppLayout>
</template>
