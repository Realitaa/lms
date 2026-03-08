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
import type { Module } from '@/types';

const props = defineProps<{
  open: boolean;
  module: Module | null;
  courseId: number;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isDeleting = ref(false);

function onConfirm() {
  if (!props.module) return;

  isDeleting.value = true;
  router.delete(`/courses/${props.courseId}/modules/${props.module.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(`Modul "${props.module?.title}" berhasil dihapus`);
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menghapus modul: ${Object.values(errors)[0]}`);
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
        <DialogTitle>Hapus Modul</DialogTitle>
        <DialogDescription>
          Apakah Anda yakin ingin menghapus modul
          <strong>{{ module?.title }}</strong> beserta seluruh materi di dalamnya? Aksi ini tidak dapat dibatalkan.
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
