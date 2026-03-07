<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import CourseForm from '@/components/courses/CourseForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/courses';
import type { BreadcrumbItem, Course } from '@/types';
import TitleWithBack from '@/components/TitleWithBack.vue';
import { Button } from '@/components/ui/button'
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ScrollArea } from '@/components/ui/scroll-area'

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
    title: 'Manajemen Modul',
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

const tags = Array.from({ length: 50 }).map(
  (_, i, a) => `v1.2.0-beta.${a.length - i}`,
)
</script>

<template>

  <Head title="Edit Kursus" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <TitleWithBack
        back-url="/courses"
        :title="`Manajemen Modul ${course.title}`"
        :subtitle="`Kelola modul pada kursus ${course.title}`"
      />

    </div>
    <div class="w-full flex gap-4 mx-4 h-[calc(100vh-200px)]">
      <div class="w-[67%] border">
        editor
      </div>
      <Card class="w-2/7 h-full flex flex-col p-4">
        <CardHeader class="px-0">
          <CardTitle>Modul dan Materi</CardTitle>
          <CardDescription>
            Drag & Drop untuk reposisi materi atau modul. Tombol + disamping untuk menambah modul.
          </CardDescription>
          <CardAction>
            <Button size="icon" variant="ghost">
              <Plus />
            </Button>
          </CardAction>
        </CardHeader>
        <CardContent class="flex-1 overflow-hidden px-0">
          <ScrollArea class="h-full pr-3">
              <template v-for="tag in tags" :key="tag">
                <Card class="w-full max-w-sm mb-2 p-4 gap-2!">
                  <CardHeader class="px-0 pb-2! border-b">
                    <CardTitle>Pengenalan Komputer dan Penggunaan Singkat</CardTitle>
                    <CardDescription>
                      Modul / Tes
                    </CardDescription>
                    <CardAction>
                      <Button size="icon" variant="ghost" class="text-destructive hover:text-destructive">
                        <Trash2 />
                      </Button>
                    </CardAction>
                  </CardHeader>
                  <CardContent class="px-0">
                    <div v-for="i in 10" :key="i" class="flex justify-between border-b py-2">
                      <p class="font-bold text-sm">Apa itu BIOS (Basic Input Output System)?</p>
                      <Button size="icon" variant="ghost" class="text-destructive hover:text-destructive">
                        <Trash2 />
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              </template>
          </ScrollArea>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
