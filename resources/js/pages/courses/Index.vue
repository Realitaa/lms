<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { EllipsisVertical, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import DeleteCourseDialog from '@/components/courses/DeleteCourseDialog.vue';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import AppLayout from '@/layouts/AppLayout.vue';
import { edit } from '@/routes/courses';
import type { BreadcrumbItem, Course } from '@/types';

const props = defineProps<{
  courses: Course[];
}>();

const deleteDialogOpen = ref(false);
const selectedCourse = ref<Course | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Kursus',
    href: '#',
  },
];

function openDeleteDialog(course: Course) {
  selectedCourse.value = course;
  deleteDialogOpen.value = true;
}
</script>

<template>

  <Head title="Kursus" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-between p-4">
      <h1 class="text-2xl font-bold tracking-tight">Kursus</h1>
      <Button as-child>
        <Link href="/courses/create">
          <Plus class="mr-2 h-4 w-4" />
          Buat Kursus
        </Link>
      </Button>
    </div>

    <!-- Empty state -->
    <div v-if="props.courses.length === 0"
      class="flex flex-col items-center justify-center gap-4 rounded-xl border border-dashed p-16 mx-4 text-center">
      <div class="text-muted-foreground">
        <p class="text-lg font-medium">Belum ada kursus</p>
        <p class="text-sm">Buat kursus pertama Anda untuk memulai</p>
      </div>
      <Button as-child>
        <Link href="/courses/create">
          <Plus class="mr-2 h-4 w-4" />
          Buat Kursus
        </Link>
      </Button>
    </div>

    <!-- Course cards grid -->
    <div v-else class="flex flex-wrap gap-4 rounded-xl p-4 h-fit items-stretch">
      <Card v-for="course in props.courses" :key="course.id" class="flex w-full max-w-sm flex-col overflow-hidden">
        <img v-if="course.cover_image" :src="`/storage/${course.cover_image}`" :alt="course.title"
          class="aspect-video w-full object-cover -mt-6" />
        <CardHeader>
          <CardTitle class="text-2xl">{{ course.title }}</CardTitle>
          <CardDescription class="line-clamp-2">
            {{ course.description || 'Tidak ada deskripsi' }}
          </CardDescription>
        </CardHeader>
        <CardContent class="grow">
          <span class="text-sm text-muted-foreground">{{ course.code }}</span>
        </CardContent>
        <CardFooter class="mt-auto flex w-full gap-2">
          <Button class="w-[42%]" as-child>
            <Link :href="edit.url(course.id)">Atur Modul</Link>
          </Button>
          <Button variant="outline" class="w-[42%]">Statistik</Button>
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline">
                <EllipsisVertical class="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuGroup>
                <DropdownMenuItem as-child>
                  <Link :href="edit.url(course.id)">Ubah</Link>
                </DropdownMenuItem>
                <DropdownMenuItem @click="openDeleteDialog(course)">
                  Hapus
                </DropdownMenuItem>
              </DropdownMenuGroup>
            </DropdownMenuContent>
          </DropdownMenu>
        </CardFooter>
      </Card>
    </div>

    <DeleteCourseDialog v-model:open="deleteDialogOpen" :course="selectedCourse" />
  </AppLayout>
</template>
