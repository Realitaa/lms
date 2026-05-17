<script setup lang="ts">
import { Button } from '@/components/ui/button';

const props = defineProps<{
  questions: any[];
  answers: Record<number, number | null>;
  currentIndex: number;
  doubtful: Set<number>;
}>();

const emit = defineEmits<{
  select: [index: number];
}>();

const getButtonVariant = (index: number) => {
  const question = props.questions[index];
  if (!question) return 'outline';

  if (index === props.currentIndex) return 'default';
  if (props.answers[question.id]) return 'secondary';
  return 'outline';
};

const getButtonClass = (index: number) => {
  const question = props.questions[index];
  if (!question) return '';

  const classes: string[] = ['cursor-pointer'];

  if (index === props.currentIndex) {
    classes.push('ring-2 ring-primary');
  }

  if (props.doubtful.has(question.id)) {
    classes.push('!bg-yellow-200 dark:!bg-yellow-800 !border-yellow-400');
  } else if (props.answers[question.id]) {
    classes.push('!bg-green-100 dark:!bg-green-900 !border-green-300');
  }

  return classes.join(' ');
};
</script>

<template>
  <div class="m-4 gap-2 grid grid-cols-10">
    <Button v-for="(question, index) in questions" :key="question.id" :variant="getButtonVariant(index)"
      :class="getButtonClass(index)" size="sm" @click="emit('select', index)">
      {{ index + 1 }}
    </Button>
  </div>
  <div class="mx-4 flex gap-4 text-xs text-muted-foreground">
    <span class="flex items-center gap-1">
      <span class="w-3 h-3 rounded-sm bg-green-100 dark:bg-green-900 border border-green-300"></span> Terjawab
    </span>
    <span class="flex items-center gap-1">
      <span class="w-3 h-3 rounded-sm bg-yellow-200 dark:bg-yellow-800 border border-yellow-400"></span> Ragu-ragu
    </span>
    <span class="flex items-center gap-1">
      <span class="w-3 h-3 rounded-sm bg-white dark:bg-transparent border"></span> Belum dijawab
    </span>
  </div>
</template>