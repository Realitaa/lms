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
  moduleId: number | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const title = ref('');
const isSubmitting = ref(false);

function onSubmit() {
  if (!title.value.trim() || !props.moduleId) return;

  isSubmitting.value = true;
  router.post(`/courses/modules/${props.moduleId}/lessons`, {
    title: title.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Materi berhasil ditambahkan');
      title.value = '';
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menambahkan materi: ${Object.values(errors)[0]}`);
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
        <DialogTitle>Tambah Materi</DialogTitle>
        <DialogDescription>
          Masukkan judul materi baru untuk modul ini.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="onSubmit" class="space-y-4">
        <div class="space-y-2">
          <Label for="lesson-title">Judul Materi</Label>
          <Input id="lesson-title" v-model="title" placeholder="Masukkan judul materi" autofocus />
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
