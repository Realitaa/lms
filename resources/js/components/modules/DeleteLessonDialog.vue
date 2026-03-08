<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import type { Lesson } from '@/types';

const props = defineProps<{
  open: boolean;
  lesson: Lesson | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isDeleting = ref(false);

function onConfirm() {
  if (!props.lesson) return;

  isDeleting.value = true;
  router.delete(`/courses/lessons/${props.lesson.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(`Materi "${props.lesson?.title}" berhasil dihapus`);
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menghapus materi: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isDeleting.value = false;
    },
  });
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Hapus Materi</DialogTitle>
        <DialogDescription>
          Apakah Anda yakin ingin menghapus materi
          <strong>{{ lesson?.title }}</strong>? Aksi ini tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button type="button" variant="outline" @click="emit('update:open', false)">
          Batal
        </Button>
        <Button type="button" variant="destructive" :disabled="isDeleting" @click="onConfirm">
          Hapus
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
