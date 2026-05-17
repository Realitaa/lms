<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

import type { BreadcrumbItem } from '@/types';
import Title from '@/components/Title.vue';
import ContentRenderer from '@/components/courses/ContentRenderer.vue';
import Discussion from '@/components/discussion/Discussion.vue';

import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from '@/components/ui/drawer'
import { Button } from '@/components/ui/button';
import student from '@/routes/student';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import LessonLists from '@/components/learning/LessonLists.vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
  course: any;
  currentLesson: any;
  lessonHtml: string;
  modules: any[];
  progress: Record<number, any>;
  quizAttempts: Record<number, any[]>;
  accessMap: Record<string, boolean>;
  threads?: any[];
  discussionCourses?: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Kursus Saya',
    href: student.index().url,
  },
  {
    title: props.course?.title || 'Kursus',
    href: '#',
  },
  {
    title: props.currentLesson?.title || 'Materi',
    href: '#',
  },
];

const openDiscussion = ref(false);
const { auth } = usePage().props;

// Calculate total lessons
const totalLessons = computed(() => {
  return props.modules?.reduce((sum: number, m: any) => sum + (m.lessons?.length || 0), 0) || 0;
});

// Calculate completed lessons
const completedLessons = computed(() => {
  if (!props.progress) return 0;
  return Object.values(props.progress).filter((p: any) => p.is_completed).length;
});

// Navigation: find previous and next lessons
const flatLessons = computed(() => {
  const lessons: any[] = [];
  props.modules?.forEach((module: any) => {
    module.lessons?.forEach((lesson: any) => {
      lessons.push({ ...lesson, moduleTitle: module.title });
    });
  });
  return lessons;
});

const currentIndex = computed(() => {
  return flatLessons.value.findIndex((l: any) => l.id === props.currentLesson?.id);
});

const previousLesson = computed(() => {
  const idx = currentIndex.value;
  return idx > 0 ? flatLessons.value[idx - 1] : null;
});

const nextLesson = computed(() => {
  const idx = currentIndex.value;
  return idx < flatLessons.value.length - 1 ? flatLessons.value[idx + 1] : null;
});

const canAccessNext = computed(() => {
  if (!nextLesson.value) return false;
  return props.accessMap?.[`lesson_${nextLesson.value.id}`] ?? false;
});

// Check if current lesson has quizzes
const lessonQuizzes = computed(() => {
  return props.currentLesson?.quizzes || [];
});

const isQuizPassed = (quizId: number) => {
  const attempts = props.quizAttempts?.[quizId];
  if (!attempts) return false;
  return Array.isArray(attempts) && attempts.some((a: any) => a.is_passed);
};

const getBestScore = (quizId: number) => {
  const attempts = props.quizAttempts?.[quizId];
  if (!attempts || !Array.isArray(attempts) || attempts.length === 0) return null;
  return Math.max(...attempts.map((a: any) => a.score ?? 0));
};
</script>

<template>

  <Head :title="currentLesson?.title || 'Belajar'" />

  <AppLayout :breadcrumbs="breadcrumbs" class="bg-sky-100 dark:bg-black">
    <div class="p-4 flex flex-col gap-4">
      <div
        class="space-y-2 flex justify-between items-center p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
        <div class="space-y-2">
          <Title :title="course?.title || 'Kursus'"
            subtitle="Silahkan selesaikan kelas dan meluluskan kuis pada materi agar dapat melanjutkan ke materi selanjutnya." />
          <p class="text-sm text-muted-foreground">{{ completedLessons }}/{{ totalLessons }} Materi selesai</p>
        </div>
        <!-- Button to change between course and discussion forum -->
        <Button variant="outline" @click="openDiscussion = !openDiscussion">{{ openDiscussion ? 'Kembali ke Materi' :
          'Forum Diskusi' }}</Button>
      </div>

      <div v-if="!openDiscussion" class="flex flex-col lg:flex-row w-full gap-4">

        <!-- Main Content (Readable width) -->
        <div
          class="space-y-2 py-4 pl-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm w-full max-w-[750px] mx-auto xl:mx-0">
          <ScrollArea class="h-[calc(100vh-280px)] pr-4">
            <!-- Lesson content rendered from TipTap JSON -->
            <ContentRenderer v-if="lessonHtml" :content="lessonHtml" />
            <div v-else class="text-muted-foreground text-center p-8">
              Konten materi belum tersedia.
            </div>

            <!-- Lesson quizzes section -->
            <template v-if="lessonQuizzes.length > 0">
              <div v-for="quiz in lessonQuizzes" :key="quiz.id"
                class="flex mt-4 justify-between items-center border rounded-2xl p-4"
                :class="isQuizPassed(quiz.id) ? 'bg-green-50 dark:bg-green-950 border-green-200' : 'bg-orange-50 dark:bg-orange-950 border-orange-200'">
                <div>
                  <p class="font-medium">{{ quiz.title }}</p>
                  <p class="text-sm text-muted-foreground">
                    Selesaikan kuis dengan nilai minimal {{ quiz.passing_score }} untuk melanjutkan ke materi selanjutnya.
                  </p>
                  <p v-if="getBestScore(quiz.id) !== null" class="text-sm mt-1"
                    :class="isQuizPassed(quiz.id) ? 'text-green-600' : 'text-red-600'">
                    Nilai terbaik: {{ getBestScore(quiz.id) }} / 100
                    <span v-if="isQuizPassed(quiz.id)" class="font-semibold">✓ Lulus</span>
                    <span v-else class="font-semibold">✗ Belum Lulus</span>
                  </p>
                </div>
                <Button as-child :variant="isQuizPassed(quiz.id) ? 'outline' : 'default'">
                  <Link :href="`/learning/${course.slug}/quiz/${quiz.id}`">
                    {{ isQuizPassed(quiz.id) ? 'Ulangi Kuis' : 'Mulai Kuis' }}
                  </Link>
                </Button>
              </div>
            </template>

            <!-- Navigation buttons -->
            <div class="flex justify-between mt-6 gap-2">
              <Button v-if="previousLesson" as-child variant="outline">
                <Link :href="`/learning/${course.slug}/lessons/${previousLesson.slug}`">
                  <ChevronLeft class="w-4 h-4" />
                  {{ previousLesson.title }}
                </Link>
              </Button>
              <div v-else></div>

              <Button v-if="nextLesson" as-child :variant="canAccessNext ? 'default' : 'outline'" :disabled="!canAccessNext">
                <Link v-if="canAccessNext" :href="`/learning/${course.slug}/lessons/${nextLesson.slug}`">
                  {{ nextLesson.title }}
                  <ChevronRight class="w-4 h-4" />
                </Link>
                <span v-else class="flex items-center gap-1 opacity-50 cursor-not-allowed">
                  {{ nextLesson.title }}
                  <ChevronRight class="w-4 h-4" />
                </span>
              </Button>
            </div>
          </ScrollArea>
        </div>

        <!-- Sidebar (takes remaining space) -->
        <div
          class="space-y-2 py-4 pl-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm w-full hidden xl:block xl:flex-1">
          <h2 class="text-lg font-medium">Materi Belajar</h2>
          <LessonLists :modules="modules" :current-lesson-id="currentLesson?.id" :course-slug="course?.slug"
            :progress="progress" :quiz-attempts="quizAttempts" :access-map="accessMap" />
        </div>

        <div class="xl:hidden">
          <Drawer>
            <DrawerTrigger as-child>
              <div class="w-full absolute bottom-0 left-0 right-0 bg-white">
                <Button class="w-full" variant="outline">Buka Materi</Button>
              </div>
            </DrawerTrigger>
            <DrawerContent>
              <DrawerHeader>
                <DrawerTitle>Materi Belajar</DrawerTitle>
                <DrawerDescription>
                  <LessonLists :modules="modules" :current-lesson-id="currentLesson?.id"
                    :course-slug="course?.slug" :progress="progress" :quiz-attempts="quizAttempts"
                    :access-map="accessMap" />
                </DrawerDescription>
              </DrawerHeader>
              <DrawerFooter>
                <DrawerClose as-child>
                  <Button variant="outline">Tutup</Button>
                </DrawerClose>
              </DrawerFooter>
            </DrawerContent>
          </Drawer>
        </div>

      </div>

      <div v-else class="flex flex-col lg:flex-row w-full gap-4">
        <div class="w-full space-y-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm p-4">
          <Discussion :course="course" :courses="discussionCourses" :threads="threads" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>