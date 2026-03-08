<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import * as z from 'zod';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

const props = defineProps<{
  open: boolean;
  moduleId: number | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isSubmitting = ref(false);

const formSchema = toTypedSchema(
  z.object({
    title: z.string().min(1, 'Judul wajib diisi').max(255),
    score: z.coerce.number({
      required_error: 'Skor kelulusan wajib diisi',
      invalid_type_error: 'Skor harus berupa angka',
    }).min(0, 'Skor minimal 0').max(100, 'Skor maksimal 100'),
    time_limit: z.coerce.number({
      required_error: 'Batas waktu wajib diisi',
      invalid_type_error: 'Batas waktu harus berupa angka',
    }).min(1, 'Batas waktu minimal 1 menit'),
  }),
);

const { handleSubmit, resetForm } = useForm({
  validationSchema: formSchema,
  initialValues: {
    title: '',
    score: 0,
    time_limit: 15,
  },
});

const onSubmit = handleSubmit((values) => {
  if (!props.moduleId) return;

  isSubmitting.value = true;
  router.post(`/courses/modules/${props.moduleId}/quizzes`, {
    title: values.title,
    score: values.score,
    time_limit: values.time_limit,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Kuis berhasil ditambahkan');
      resetForm();
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menambahkan kuis: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
});
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Tambah Kuis</DialogTitle>
        <DialogDescription>
          Masukkan data kuis baru untuk modul ini.
        </DialogDescription>
      </DialogHeader>

      <form @submit="onSubmit" class="space-y-4">
        <FormField v-slot="{ componentField }" name="title">
          <FormItem>
            <FormLabel required>Judul Kuis</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Masukkan judul kuis" v-bind="componentField" autofocus />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="score">
          <FormItem>
            <FormLabel required>Skor Kelulusan</FormLabel>
            <FormControl>
              <Input type="number" placeholder="Masukkan minimal skor kelulusan" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="time_limit">
          <FormItem>
            <FormLabel required>Batas Waktu (menit)</FormLabel>
            <FormControl>
              <Input type="number" placeholder="Masukkan batas waktu" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <DialogFooter>
          <Button type="button" variant="outline" @click="emit('update:open', false)">
            Batal
          </Button>
          <Button type="submit" :disabled="isSubmitting">
            Tambah
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
