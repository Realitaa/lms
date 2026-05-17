<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

const open = defineModel<boolean>('open');

const props = defineProps<{
  unansweredCount: number;
}>();

const emit = defineEmits<{
  confirm: [];
}>();

const handleConfirm = () => {
  emit('confirm');
  open.value = false;
};
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Akhiri Kuis</DialogTitle>
      </DialogHeader>
      <div class="flex flex-col gap-4">
        <p>Apakah anda yakin ingin mengakhiri kuis?</p>
        <p v-if="unansweredCount > 0" class="text-sm text-red-500 font-medium">
          ⚠ Anda masih memiliki {{ unansweredCount }} soal yang belum dijawab.
        </p>
        <div class="flex justify-end gap-2">
          <Button variant="outline" @click="open = false">Batal</Button>
          <Button variant="destructive" @click="handleConfirm">Ya, Selesaikan Kuis</Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
