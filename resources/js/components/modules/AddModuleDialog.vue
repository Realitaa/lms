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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
  open: boolean;
  courseId: number;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const title = ref('');
const isSubmitting = ref(false);

function onSubmit() {
  if (!title.value.trim()) return;

  isSubmitting.value = true;
  router.post(`/courses/${props.courseId}/modules`, {
    title: title.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Modul berhasil ditambahkan');
      title.value = '';
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menambahkan modul: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Tambah Modul</DialogTitle>
        <DialogDescription>
          Masukkan judul modul baru untuk kursus ini.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="onSubmit" class="space-y-4">
        <div class="space-y-2">
          <Label for="module-title">Judul Modul</Label>
          <Input id="module-title" v-model="title" placeholder="Masukkan judul modul" autofocus />
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="emit('update:open', false)">
            Batal
          </Button>
          <Button type="submit" :disabled="isSubmitting || !title.trim()">
            Tambah
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
