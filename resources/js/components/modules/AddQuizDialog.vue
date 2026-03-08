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
  router.post(`/courses/modules/${props.moduleId}/quizzes`, {
    title: title.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Kuis berhasil ditambahkan');
      title.value = '';
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menambahkan kuis: ${Object.values(errors)[0]}`);
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
        <DialogTitle>Tambah Kuis</DialogTitle>
        <DialogDescription>
          Masukkan judul kuis baru untuk modul ini.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="onSubmit" class="space-y-4">
        <div class="space-y-2">
          <Label for="quiz-title">Judul Kuis</Label>
          <Input id="quiz-title" v-model="title" placeholder="Masukkan judul kuis" autofocus />
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
