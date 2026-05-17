<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { SquareArrowRightExit, ChevronLeft, ChevronRight, CircleHelp } from 'lucide-vue-next';
import MaterialSymbolsWindow from '@/components/icons/MaterialSymbolsWindow.vue';
import Title from '@/components/Title.vue';
import { Button } from '@/components/ui/button';
import QuestionListDialog from '@/components/quiz/QuestionListDialog.vue';
import QuestionList from '@/components/quiz/QuestionList.vue';
import EndTestDialog from '@/components/quiz/EndTestDialog.vue';
import ContentRenderer from '@/components/courses/ContentRenderer.vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { tiptapJsonToHtml } from '@/utils/tiptapToHtml';

const props = defineProps<{
  course: any;
  quiz: any;
  questions: any[];
  existingAttempt?: any;
  pastAttempts?: any[];
}>();

const { auth } = usePage().props;
const userName = (auth as any).user.name;
const page = usePage();

// State
const currentIndex = ref(0);
const answers = ref<Record<number, number | null>>({});
const doubtful = ref<Set<number>>(new Set());
const questionListOpen = ref(false);
const endTestOpen = ref(false);
const quizStarted = ref(!!props.existingAttempt);
const quizFinished = ref(false);
const submitting = ref(false);

// Quiz result from flash
const quizResult = computed(() => {
  return (page.props as any).flash?.quizResult || null;
});

// Initialize answers
props.questions.forEach((q: any) => {
  answers.value[q.id] = null;
});

// Timer
const timeRemaining = ref(0);
const timerInterval = ref<ReturnType<typeof setInterval> | null>(null);

const formattedTime = computed(() => {
  const hours = Math.floor(timeRemaining.value / 3600);
  const minutes = Math.floor((timeRemaining.value % 3600) / 60);
  const seconds = timeRemaining.value % 60;
  return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
});

const startTimer = () => {
  if (!props.quiz.time_limit) return;

  let totalSeconds = props.quiz.time_limit * 60;

  // If there's an existing attempt, calculate remaining time
  if (props.existingAttempt?.started_at) {
    const startedAt = new Date(props.existingAttempt.started_at).getTime();
    const elapsed = Math.floor((Date.now() - startedAt) / 1000);
    totalSeconds = Math.max(0, totalSeconds - elapsed);
  }

  timeRemaining.value = totalSeconds;

  timerInterval.value = setInterval(() => {
    if (timeRemaining.value <= 0) {
      if (timerInterval.value) clearInterval(timerInterval.value);
      // Auto-submit when time runs out
      handleSubmit();
      return;
    }
    timeRemaining.value--;
  }, 1000);
};

// Start quiz
const handleStartQuiz = () => {
  router.post(`/learning/quiz/${props.quiz.id}/start`, {}, {
    preserveState: true,
    onSuccess: () => {
      quizStarted.value = true;
      startTimer();
    },
  });
};

// Submit quiz
const handleSubmit = () => {
  if (submitting.value) return;
  submitting.value = true;

  if (timerInterval.value) clearInterval(timerInterval.value);

  const answerPayload = props.questions.map((q: any) => ({
    question_id: q.id,
    option_id: answers.value[q.id] || null,
  }));

  router.post(`/learning/quiz/${props.quiz.id}/submit`, {
    answers: answerPayload,
  }, {
    preserveState: true,
    onSuccess: () => {
      quizFinished.value = true;
      submitting.value = false;
    },
    onError: () => {
      submitting.value = false;
    },
  });
};

// Navigation
const currentQuestion = computed(() => props.questions[currentIndex.value]);

const goToQuestion = (index: number) => {
  if (index >= 0 && index < props.questions.length) {
    currentIndex.value = index;
  }
};

const goToPrevious = () => goToQuestion(currentIndex.value - 1);
const goToNext = () => goToQuestion(currentIndex.value + 1);

const toggleDoubtful = () => {
  const qId = currentQuestion.value.id;
  if (doubtful.value.has(qId)) {
    doubtful.value.delete(qId);
  } else {
    doubtful.value.add(qId);
  }
};

const selectAnswer = (optionId: number) => {
  answers.value[currentQuestion.value.id] = optionId;
};

// Unanswered count
const unansweredCount = computed(() => {
  return props.questions.filter((q: any) => !answers.value[q.id]).length;
});

// Render question/option text (TipTap JSON -> HTML)
const renderContent = (content: any): string => {
  if (!content) return '';
  if (typeof content === 'string') return content;
  try {
    return tiptapJsonToHtml(content);
  } catch {
    return JSON.stringify(content);
  }
};

// Lifecycle
onMounted(() => {
  if (quizStarted.value && !quizResult.value) {
    startTimer();
  }
});

onUnmounted(() => {
  if (timerInterval.value) clearInterval(timerInterval.value);
});

const goBackToLesson = () => {
  // Navigate back based on quiz's quizable type
  window.history.back();
};
</script>

<template>

  <Head :title="quiz?.title || 'Kuis'" />

  <Teleport to="body">
    <div class="w-full min-h-[100vh] bg-sky-100 dark:bg-black">
      <div class="p-4 flex flex-col gap-4">
        <!-- Header -->
        <div
          class="space-y-2 flex flex-col md:flex-row justify-between md:items-center p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
          <div class="space-y-2">
            <Title :title="quiz?.title || 'Kuis'"
              :subtitle="`Selesaikan kursus dengan minimal nilai ${quiz?.passing_score || 0} untuk melanjutkan ke materi selanjutnya.`" />
          </div>
          <div class="flex items-center gap-2 text-nowrap justify-between md:justify-end">
            <div class="flex flex-col md:items-end">
              <p class="font-semibold">{{ userName }}</p>
              <p v-if="quizStarted && !quizFinished && !quizResult" class="text-sm text-muted-foreground">Waktu tersisa: <span
                  class="tabular-nums" :class="timeRemaining < 60 ? 'text-red-500 font-bold' : ''">{{ formattedTime
                  }}</span></p>
            </div>
            <div v-if="quizStarted && !quizFinished && !quizResult">
              <Button variant="ghost" size="icon" class="lg:hidden cursor-pointer" @click="questionListOpen = true">
                <MaterialSymbolsWindow class="w-4 h-4" />
              </Button>
              <Button variant="ghost" size="icon" class="text-red-500 hover:text-red-600 cursor-pointer"
                @click="endTestOpen = true">
                <SquareArrowRightExit class="w-4 h-4" />
              </Button>
            </div>
          </div>
        </div>

        <!-- Pre-start screen -->
        <div v-if="!quizStarted && !quizResult"
          class="p-8 border rounded-2xl bg-white dark:bg-transparent shadow-sm text-center">
          <h2 class="text-2xl font-bold mb-4">{{ quiz?.title }}</h2>
          <div class="space-y-2 text-muted-foreground mb-6">
            <p>Jumlah Soal: <strong>{{ questions.length }}</strong></p>
            <p v-if="quiz?.time_limit">Batas Waktu: <strong>{{ quiz.time_limit }} menit</strong></p>
            <p>Nilai Minimum Kelulusan: <strong>{{ quiz?.passing_score }}</strong></p>
          </div>

          <!-- Past attempts -->
          <div v-if="pastAttempts && pastAttempts.length > 0" class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Riwayat Percobaan</h3>
            <div class="space-y-2 max-w-md mx-auto">
              <div v-for="attempt in pastAttempts" :key="attempt.id"
                class="flex justify-between items-center p-3 border rounded-lg"
                :class="attempt.is_passed ? 'bg-green-50 dark:bg-green-950' : 'bg-red-50 dark:bg-red-950'">
                <span>Percobaan {{ attempt.attempt_number }}</span>
                <span class="font-semibold" :class="attempt.is_passed ? 'text-green-600' : 'text-red-600'">
                  {{ attempt.score }} / 100
                  {{ attempt.is_passed ? '✓' : '✗' }}
                </span>
              </div>
            </div>
          </div>

          <Button size="lg" @click="handleStartQuiz" class="px-8">
            Mulai Kuis
          </Button>
        </div>

        <!-- Quiz result screen -->
        <div v-else-if="quizResult"
          class="p-8 border rounded-2xl bg-white dark:bg-transparent shadow-sm text-center">
          <h2 class="text-2xl font-bold mb-4">Hasil Kuis</h2>
          <div class="text-6xl font-bold mb-4"
            :class="quizResult.isPassed ? 'text-green-500' : 'text-red-500'">
            {{ quizResult.score }}
          </div>
          <p class="text-lg mb-2">
            {{ quizResult.earnedPoints }} dari {{ quizResult.totalPoints }} poin
          </p>
          <p class="text-xl font-semibold mb-6"
            :class="quizResult.isPassed ? 'text-green-600' : 'text-red-600'">
            {{ quizResult.isPassed ? '🎉 Selamat, Anda Lulus!' : '😔 Maaf, Anda Belum Lulus' }}
          </p>
          <p class="text-sm text-muted-foreground mb-6">
            Nilai minimum kelulusan: {{ quizResult.passingScore }}
          </p>
          <div class="flex gap-4 justify-center">
            <Button variant="outline" @click="goBackToLesson">
              Kembali ke Materi
            </Button>
            <Button v-if="!quizResult.isPassed" @click="() => { quizFinished = false; quizStarted = false; router.reload(); }">
              Coba Lagi
            </Button>
          </div>
        </div>

        <!-- Quiz questions -->
        <div v-else class="flex gap-4">
          <div class="w-full">
            <div class="p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
              <h2 class="text-xl font-semibold ml-4">Soal {{ currentIndex + 1 }}</h2>
              <div class="px-4">
                <ContentRenderer :content="renderContent(currentQuestion?.question_text)" />
              </div>
              <RadioGroup :model-value="answers[currentQuestion?.id]?.toString() || ''"
                @update:model-value="(val: any) => { if (val) selectAnswer(parseInt(val)) }" class="p-4">
                <div v-for="option in currentQuestion?.options" :key="option.id"
                  class="flex items-start space-x-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors cursor-pointer"
                  :class="answers[currentQuestion?.id] === option.id ? 'bg-primary/5 border-primary' : ''">
                  <RadioGroupItem :id="`option-${option.id}`" :value="option.id.toString()" class="mt-1" />
                  <Label :for="`option-${option.id}`" class="cursor-pointer flex-1">
                    <ContentRenderer :content="renderContent(option.option_text)" class="!p-0" />
                  </Label>
                </div>
              </RadioGroup>
              <div class="flex justify-between mt-4 px-4">
                <Button variant="outline" class="cursor-pointer" :disabled="currentIndex === 0" @click="goToPrevious">
                  <ChevronLeft class="w-4 h-4" />
                  Sebelumnya
                </Button>
                <Button variant="outline" class="cursor-pointer" @click="toggleDoubtful"
                  :class="doubtful.has(currentQuestion?.id) ? 'bg-yellow-100 border-yellow-400 dark:bg-yellow-900' : ''">
                  <CircleHelp class="w-4 h-4" />
                  Ragu-Ragu
                </Button>
                <Button v-if="currentIndex < questions.length - 1" variant="outline" class="cursor-pointer"
                  @click="goToNext">
                  Selanjutnya
                  <ChevronRight class="w-4 h-4" />
                </Button>
                <Button v-else variant="default" class="cursor-pointer" @click="endTestOpen = true">
                  Selesai
                </Button>
              </div>
            </div>
          </div>
          <div class="p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm max-w-150 hidden lg:block">
            <h2 class="text-xl font-semibold ml-4">Daftar Soal</h2>
            <QuestionList :questions="questions" :answers="answers" :current-index="currentIndex" :doubtful="doubtful"
              @select="goToQuestion" />
          </div>
        </div>
      </div>
    </div>
    <QuestionListDialog v-model:open="questionListOpen" :questions="questions" :answers="answers"
      :current-index="currentIndex" :doubtful="doubtful" @select="goToQuestion" />
    <EndTestDialog v-model:open="endTestOpen" :unanswered-count="unansweredCount" @confirm="handleSubmit" />
  </Teleport>
</template>