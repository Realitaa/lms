<script setup lang="ts">
import QuestionList from '@/components/quiz/QuestionList.vue';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

const open = defineModel<boolean>('open');

const props = defineProps<{
  questions: any[];
  answers: Record<number, number | null>;
  currentIndex: number;
  doubtful: Set<number>;
}>();

const emit = defineEmits<{
  select: [index: number];
}>();

const handleSelect = (index: number) => {
  emit('select', index);
  open.value = false;
};
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="min-w-150">
      <DialogHeader>
        <DialogTitle class="pl-4">Daftar Soal</DialogTitle>
      </DialogHeader>
      <QuestionList :questions="questions" :answers="answers" :current-index="currentIndex" :doubtful="doubtful"
        @select="handleSelect" />
    </DialogContent>
  </Dialog>
</template>
