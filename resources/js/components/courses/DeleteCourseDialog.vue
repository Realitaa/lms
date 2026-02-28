<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { destroy } from '@/routes/courses';
import type { Course } from '@/types';

const props = defineProps<{
  open: boolean;
  course: Course | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isDeleting = ref(false);

function onConfirm() {
  if (!props.course) return;

  isDeleting.value = true;
  router.delete(destroy.url(props.course.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(`Kursus ${props.course?.title} berhasil dihapus`);
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menghapus kursus ${props.course?.title}: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isDeleting.value = false;
    },
  });
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-106.25">
      <DialogHeader>
        <DialogTitle>Hapus Kursus</DialogTitle>
        <DialogDescription>
          Apakah Anda yakin ingin menghapus kursus
          <strong>{{ course?.title }}</strong>? Aksi ini tidak dapat dibatalkan.
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
