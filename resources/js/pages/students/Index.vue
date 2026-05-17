<script setup lang="ts">
import { Head, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import student from '@/routes/student';
import Title from '@/components/Title.vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  latestLessons: any[];
  enrolledCourses: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: student.index().url,
  },
];

const { auth } = usePage().props;
const userName = auth.user.name;
</script>

<template>

  <Head title="Beranda" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 flex flex-col gap-8">
      <Title title="Dashboard" :subtitle="`Selamat Belajar ${userName}`" />

      <!-- Show latest users learning course -->
      <div>
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold tracking-tight">Lanjutkan Proses Belajar Anda</h2>
        </div>
        
        <div v-if="latestLessons.length === 0" class="text-muted-foreground flex items-center justify-center p-8 border border-dashed rounded-xl">
          Belum ada aktivitas belajar.
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card v-for="progress in latestLessons" :key="progress.id" class="flex flex-col">
            <CardHeader class="pb-2">
              <CardTitle class="text-lg line-clamp-1">{{ progress.lesson.module.course.title }}</CardTitle>
              <CardDescription class="line-clamp-1">{{ progress.lesson.module.title }}</CardDescription>
            </CardHeader>
            <CardContent class="grow">
              <p class="text-sm font-medium line-clamp-2">{{ progress.lesson.title }}</p>
              <p class="text-xs text-muted-foreground mt-2">
                Terakhir diakses: {{ new Date(progress.last_accessed_at || progress.created_at).toLocaleDateString('id-ID') }}
              </p>
            </CardContent>
            <CardFooter>
              <Button as-child variant="default" class="w-full">
                <Link :href="`/learning/${progress.lesson.module.course.slug}/lessons/${progress.lesson.slug}`">
                  Lanjutkan
                </Link>
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>

      <div>
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold tracking-tight">Kelas yang Memulai</h2>
          <Button as-child variant="link">
            <Link :href="student.discover().url">Lihat Semua</Link>
          </Button>
        </div>
        
        <div v-if="enrolledCourses.length === 0" class="flex flex-col items-center justify-center gap-4 rounded-xl border border-dashed p-16 text-center">
            <div class="text-muted-foreground">
              <p class="text-lg font-medium">Anda belum mendaftar di kelas apapun</p>
            </div>
            <Button as-child>
              <Link :href="student.discover().url">Cari Kursus</Link>
            </Button>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-stretch">
          <Card v-for="course in enrolledCourses" :key="course.id" class="flex flex-col overflow-hidden">
            <img v-if="course.cover_image" :src="`/storage/${course.cover_image}`" :alt="course.title" class="aspect-video w-full object-cover" />
            <div v-else class="aspect-video w-full bg-muted flex items-center justify-center text-muted-foreground">
              No Image
            </div>
            <CardHeader class="flex-grow">
              <CardTitle class="text-lg line-clamp-2">{{ course.title }}</CardTitle>
            </CardHeader>
            <CardFooter class="mt-auto">
              <Button as-child variant="outline" class="w-full">
                <Link :href="`/learning/${course.slug}/lessons/${course.modules?.[0]?.lessons?.[0]?.slug || ''}`">
                  Buka Kelas
                </Link>
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
