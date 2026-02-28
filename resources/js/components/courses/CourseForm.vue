<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { ImagePlus } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref, watch } from 'vue';
import * as z from 'zod';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Course } from '@/types';

const props = defineProps<{
  course?: Course;
  isSubmitting: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', data: FormData): void;
}>();

const isEditing = !!props.course;

const imagePreview = ref<string | null>(
  props.course?.cover_image
    ? `/storage/${props.course.cover_image}`
    : null,
);
const imageFile = ref<File | null>(null);

const formSchema = toTypedSchema(
  z.object({
    title: z.string().min(1, 'Judul wajib diisi').max(255),
    code: z.string().min(1, 'Kode kursus wajib diisi').max(255),
    description: z.string().optional(),
  }),
);

const { handleSubmit, resetForm, setValues } = useForm({
  validationSchema: formSchema,
  initialValues: {
    title: props.course?.title ?? '',
    code: props.course?.code ?? '',
    description: props.course?.description ?? '',
  },
});

watch(
  () => props.course,
  (course) => {
    if (course) {
      setValues({
        title: course.title,
        code: course.code,
        description: course.description ?? '',
      });
      imagePreview.value = course.cover_image
        ? `/storage/${course.cover_image}`
        : null;
    } else {
      resetForm();
      imagePreview.value = null;
      imageFile.value = null;
    }
  },
);

function onImageChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    imageFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
}

const onSubmit = handleSubmit((values) => {
  const formData = new FormData();
  formData.append('title', values.title);
  formData.append('code', values.code);
  if (values.description) {
    formData.append('description', values.description);
  }
  if (imageFile.value) {
    formData.append('cover_image', imageFile.value);
  }

  emit('submit', formData);
});
</script>

<template>
  <form @submit="onSubmit">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
      <!-- Left column: Form inputs -->
      <div class="space-y-6 md:col-span-2">
        <Card>
          <CardHeader>
            <CardTitle>Detail Kursus</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <FormField v-slot="{ componentField }" name="title">
              <FormItem>
                <FormLabel required>Judul</FormLabel>
                <FormControl>
                  <Input type="text" placeholder="Masukkan judul kursus" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="code">
              <FormItem>
                <FormLabel required>Kode Kursus</FormLabel>
                <FormControl>
                  <Input type="text" placeholder="Contoh: CS101" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="description">
              <FormItem>
                <FormLabel>Deskripsi</FormLabel>
                <FormControl>
                  <Textarea placeholder="Masukkan deskripsi kursus" class="min-h-32 resize-y" v-bind="componentField" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
          </CardContent>
        </Card>
      </div>

      <!-- Right column: Image upload -->
      <div class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Gambar Sampul <span class="text-red-500">*</span></CardTitle>
          </CardHeader>
          <CardContent>
            <label for="cover-image-input"
              class="group relative block cursor-pointer overflow-hidden rounded-lg border-2 border-dashed border-muted-foreground/25 transition-colors hover:border-muted-foreground/50">
              <div v-if="imagePreview" class="relative aspect-video w-full">
                <img :src="imagePreview" alt="Cover preview" class="h-full w-full object-cover" />
                <div
                  class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                  <span class="text-sm font-medium text-white">
                    Ganti Gambar
                  </span>
                </div>
              </div>

              <div v-else
                class="flex aspect-video w-full flex-col items-center justify-center gap-2 p-6 text-muted-foreground">
                <ImagePlus class="h-10 w-10" />
                <span class="text-sm font-medium">
                  Klik untuk unggah gambar
                </span>
                <span class="text-xs">
                  JPG, PNG, atau WebP (maks. 2MB)
                </span>
              </div>

              <input id="cover-image-input" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                class="sr-only" @change="onImageChange" />
            </label>
          </CardContent>
        </Card>

        <div class="flex gap-2">
          <Button type="submit" class="w-full" :disabled="isSubmitting">
            {{ isEditing ? 'Simpan Perubahan' : 'Buat Kursus' }}
          </Button>
        </div>
      </div>
    </div>
  </form>
</template>
