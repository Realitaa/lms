<script setup lang="ts">
import QuestionList from '@/components/quiz/QuestionList.vue';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

const open = defineModel<boolean>('open');
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="min-w-150">
      <DialogHeader>
        <DialogTitle class="pl-4">Daftar Soal</DialogTitle>
      </DialogHeader>
      <QuestionList />
    </DialogContent>
  </Dialog>
</template>
