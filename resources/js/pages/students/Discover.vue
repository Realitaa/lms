<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Title from '@/components/Title.vue';
import { Input } from '@/components/ui/input';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem, Course } from '@/types';

interface PaginationData {
  data: Course[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const props = defineProps<{
  courses: PaginationData;
  filters: { search?: string };
}>();

const search = ref(props.filters.search || '');

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    router.get(
      '/discover',
      { search: value },
      { preserveState: true, preserveScroll: true, replace: true }
    );
  }, 300);
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Discover Course',
    href: '',
  },
];

function handlePageChange(page: number) {
  router.get(
    '/discover',
    { search: search.value, page },
    { preserveState: true, preserveScroll: true }
  );
}

function enrollCourse(courseId: number) {
  router.post(`/courses/${courseId}/enroll`, {}, { preserveScroll: true, preserveState: true });
}
</script>

<template>
  <Head title="Discover Course" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 flex flex-col gap-6">
      <div class="flex items-center justify-between">
        <Title title="Discover Course" />
        <div class="w-full max-w-sm">
          <Input 
            v-model="search" 
            type="search" 
            placeholder="Cari kursus..." 
          />
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="props.courses.data.length === 0"
        class="flex flex-col items-center justify-center gap-4 rounded-xl border border-dashed p-16 text-center">
        <div class="text-muted-foreground">
          <p class="text-lg font-medium">Tidak ada kursus yang ditemukan</p>
          <p class="text-sm">Coba gunakan kata kunci pencarian yang lain.</p>
        </div>
      </div>

      <!-- Course cards grid -->
      <div v-else class="flex flex-wrap gap-4 h-fit items-stretch">
        <Card v-for="course in props.courses.data" :key="course.id" class="flex w-full sm:w-[calc(50%-0.5rem)] md:max-w-sm flex-col overflow-hidden">
          <img v-if="course.cover_image" :src="`/storage/${course.cover_image}`" :alt="course.title"
            class="aspect-video w-full object-cover" />
          <div v-else class="aspect-video w-full bg-muted flex items-center justify-center text-muted-foreground">
            No Image
          </div>
          <CardHeader>
            <CardTitle class="text-2xl">{{ course.title }}</CardTitle>
            <CardDescription class="line-clamp-2">
              {{ course.description || 'Tidak ada deskripsi' }}
            </CardDescription>
          </CardHeader>
          <CardContent class="grow">
            <span class="text-sm text-muted-foreground">Kode: {{ course.code }}</span>
          </CardContent>
          <CardFooter class="mt-auto flex w-full">
            <Button class="w-full" :disabled="course.is_enrolled" @click="enrollCourse(course.id)">
              {{ course.is_enrolled ? 'Sudah Diikuti' : 'Ikuti Kursus' }}
            </Button>
          </CardFooter>
        </Card>
      </div>

      <!-- Pagination -->
      <div class="mt-4 flex justify-center" v-if="props.courses.total > props.courses.per_page">
        <Pagination
          v-slot="{ page }"
          :total="props.courses.total"
          :sibling-count="1"
          show-edges
          :default-page="props.courses.current_page"
          :items-per-page="props.courses.per_page"
          @update:page="handlePageChange"
        >
          <PaginationContent v-slot="{ items }">
            <PaginationFirst />
            <PaginationPrevious />

            <template v-for="(item, index) in items">
              <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'" @click="handlePageChange(item.value)">
                  {{ item.value }}
                </Button>
              </PaginationItem>
              <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>

            <PaginationNext />
            <PaginationLast />
          </PaginationContent>
        </Pagination>
      </div>
    </div>
  </AppLayout>
</template>