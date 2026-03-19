<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogDescription,
} from '@/components/ui/dialog';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import type { Course, Lesson } from '@/types';

const props = defineProps<{
  course: Course | any;
  lessons: Lesson[];
}>();

const open = defineModel<boolean>('open');

const form = useForm<{ lesson_id: string; title: string; content: any }>({
  lesson_id: '',
  title: '',
  content: null,
});

const submitThread = () => {
  form.transform((data) => ({
    ...data,
    content: data.content ? JSON.stringify(data.content) : '',
  })).post('/discussions', {
    preserveScroll: true,
    onSuccess: () => {
      open.value = false;
      form.reset();
    }
  });
};
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="min-w-4xl">
      <DialogHeader>
        <DialogTitle>Buat Diskusi Baru</DialogTitle>
        <DialogDescription>
          Buat diskusi baru untuk materi kursus yang dipilih.
        </DialogDescription>
      </DialogHeader>
      <div class="space-y-4">
        <!-- Lesson Select -->
        <div>
          <label class="mb-1 block text-sm font-medium">Materi</label>
          <Select v-model="form.lesson_id">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih materi" />
            </SelectTrigger>
            <SelectContent>
              <template v-for="mod in (course.modules ?? [])" :key="mod.id">
                <SelectGroup>
                  <SelectLabel>{{ course.title }} — {{ mod.title }}</SelectLabel>
                  <SelectItem v-for="lesson in (mod.lessons ?? [])" :key="lesson.id" :value="String(lesson.id)">
                    {{ lesson.title }}
                  </SelectItem>
                </SelectGroup>
              </template>
            </SelectContent>
          </Select>
        </div>
        <!-- Title -->
        <div>
          <label class="mb-1 block text-sm font-medium">Judul Diskusi</label>
          <Input v-model="form.title" placeholder="Masukkan judul diskusi" />
        </div>
        <!-- Content -->
        <div>
          <label class="mb-1 block text-sm font-medium">Isi Diskusi</label>
          <RichTextEditor v-model="form.content" />
        </div>
      </div>
      <DialogFooter>
        <Button variant="outline" @click="open = false">Batal</Button>
        <Button @click="submitThread" :disabled="form.processing">
          Buat Diskusi
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>