<script setup lang="ts">
import { Check, Book, Lock, FileQuestion } from 'lucide-vue-next';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
  modules: any[];
  currentLessonId?: number;
  courseSlug: string;
  progress?: Record<number, any>;
  quizAttempts?: Record<number, any[]>;
  accessMap?: Record<string, boolean>;
}>();

const isLessonCompleted = (lessonId: number) => {
  return props.progress?.[lessonId]?.is_completed ?? false;
};

const isLessonAccessible = (lessonId: number) => {
  return props.accessMap?.[`lesson_${lessonId}`] ?? true;
};

const getLessonQuizCount = (lesson: any) => {
  return lesson.quizzes?.length ?? 0;
};

const isQuizPassed = (quizId: number) => {
  const attempts = props.quizAttempts?.[quizId];
  if (!attempts) return false;
  return attempts.some((a: any) => a.is_passed);
};
</script>

<template>
  <ScrollArea class="h-[calc(100vh-320px)] pr-4">
    <div>
      <template v-for="(module, moduleIndex) in modules" :key="module.id">
        <h3 class="text-md font-bold my-4">{{ module.title }}</h3>

        <!-- Module pre-quiz -->
        <template v-for="quiz in (module.quizzes || []).filter((q: any) => q.type === 'pre')" :key="'mq-pre-' + quiz.id">
          <Card class="mb-4 cursor-pointer"
            :class="isQuizPassed(quiz.id) ? 'bg-green-100 dark:bg-green-900' : 'bg-orange-50 dark:bg-orange-950'">
            <Link :href="`/learning/${courseSlug}/quiz/${quiz.id}`">
              <CardHeader class="flex">
                <div class="flex justify-center items-center">
                  <div class="w-6 h-6 rounded-full flex items-center justify-center"
                    :class="isQuizPassed(quiz.id) ? 'bg-green-500' : 'bg-orange-500'">
                    <Check v-if="isQuizPassed(quiz.id)" class="w-3 h-3 text-white" :stroke-width="5" />
                    <FileQuestion v-else class="w-3 h-3 text-white" :stroke-width="3" />
                  </div>
                  <div class="ml-4">
                    <CardTitle class="text-sm">{{ quiz.title }}</CardTitle>
                    <CardDescription>Kuis Modul (Pre) · Nilai min: {{ quiz.passing_score }}</CardDescription>
                  </div>
                </div>
              </CardHeader>
            </Link>
          </Card>
        </template>

        <!-- Lessons -->
        <template v-for="(lesson, lessonIndex) in module.lessons" :key="lesson.id">
          <Card class="mb-4"
            :class="[
              currentLessonId === lesson.id ? 'ring-2 ring-primary' : '',
              isLessonCompleted(lesson.id) ? 'bg-green-100 dark:bg-green-900' : 'bg-white dark:bg-slate-800',
              isLessonAccessible(lesson.id) ? 'hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer' : 'opacity-50 cursor-not-allowed'
            ]">
            <component :is="isLessonAccessible(lesson.id) ? Link : 'div'"
              :href="isLessonAccessible(lesson.id) ? `/learning/${courseSlug}/lessons/${lesson.slug}` : undefined">
              <CardHeader class="flex">
                <div class="flex justify-center items-center">
                  <div class="w-6 h-6 rounded-full flex items-center justify-center"
                    :class="isLessonCompleted(lesson.id) ? 'bg-green-500' : isLessonAccessible(lesson.id) ? 'bg-blue-500' : 'bg-gray-400'">
                    <Check v-if="isLessonCompleted(lesson.id)" class="w-3 h-3 text-white" :stroke-width="5" />
                    <Lock v-else-if="!isLessonAccessible(lesson.id)" class="w-3 h-3 text-white" :stroke-width="3" />
                    <Book v-else class="w-3 h-3 text-white" :stroke-width="3" />
                  </div>
                  <div class="ml-4">
                    <CardTitle class="text-sm">{{ lesson.title }}</CardTitle>
                    <CardDescription v-if="getLessonQuizCount(lesson) > 0">
                      {{ getLessonQuizCount(lesson) }} Kuis
                    </CardDescription>
                  </div>
                </div>
              </CardHeader>
            </component>
          </Card>
        </template>

        <!-- Module post-quiz -->
        <template v-for="quiz in (module.quizzes || []).filter((q: any) => q.type === 'post')" :key="'mq-post-' + quiz.id">
          <Card class="mb-4 cursor-pointer"
            :class="isQuizPassed(quiz.id) ? 'bg-green-100 dark:bg-green-900' : 'bg-orange-50 dark:bg-orange-950'">
            <Link :href="`/learning/${courseSlug}/quiz/${quiz.id}`">
              <CardHeader class="flex">
                <div class="flex justify-center items-center">
                  <div class="w-6 h-6 rounded-full flex items-center justify-center"
                    :class="isQuizPassed(quiz.id) ? 'bg-green-500' : 'bg-orange-500'">
                    <Check v-if="isQuizPassed(quiz.id)" class="w-3 h-3 text-white" :stroke-width="5" />
                    <FileQuestion v-else class="w-3 h-3 text-white" :stroke-width="3" />
                  </div>
                  <div class="ml-4">
                    <CardTitle class="text-sm">{{ quiz.title }}</CardTitle>
                    <CardDescription>Kuis Modul (Post) · Nilai min: {{ quiz.passing_score }}</CardDescription>
                  </div>
                </div>
              </CardHeader>
            </Link>
          </Card>
        </template>
      </template>
    </div>
  </ScrollArea>
</template>