<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Search, Plus, X, MessageSquare } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import AddThreadDialog from './AddThreadDialog.vue';
import Thread from './Thread.vue';
import { router } from '@inertiajs/vue3';
import { tiptapJsonToHtml } from '@/utils/tiptapToHtml';

const props = defineProps<{
  course: any;
  courses?: any[];
  threads?: any[];
  filters?: any;
}>();

const searchQuery = ref(props.filters?.search || '');
const selectedLessonId = ref(props.filters?.lesson_id || 'all');
const newThreadOpen = ref(false);
const selectedThread = ref<any>(null);

const openNewThread = () => {
  newThreadOpen.value = true;
};

const updateFilters = () => {
  router.get(
    `/courses/${props.course?.id}/discussions`,
    { search: searchQuery.value, lesson_id: selectedLessonId.value === 'all' ? '' : selectedLessonId.value },
    { preserveState: true, replace: true }
  );
};

const clearLessonFilter = () => {
  selectedLessonId.value = 'all';
  updateFilters();
};

let searchTimeout: any = null;
const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    updateFilters();
  }, 300);
};

const selectedLessonLabel = computed(() => {
  if (!selectedLessonId.value || selectedLessonId.value === 'all') return '';
  for (const course of (props.courses as any[]) || []) {
    for (const mod of course.modules || []) {
      const lesson = mod.lessons?.find((l: any) => String(l.id) === selectedLessonId.value);
      if (lesson) return lesson.title;
    }
  }
  return '';
});

const selectThread = (thread: any) => {
  selectedThread.value = thread;
};

watch(() => props.threads, (newThreads: any[] | undefined) => {
  if (selectedThread.value && newThreads) {
    const updated = newThreads.find((t: any) => t.id === selectedThread.value.id);
    if (updated) {
      selectedThread.value = updated;
    }
  }
}, { deep: true });

watch(() => selectedLessonId.value, () => {
  selectedThread.value = null;
});

const renderPreview = (content: string) => {
  if (!content) return '';
  try {
    const json = JSON.parse(content);
    const html = tiptapJsonToHtml(json);
    return html.replace(/<[^>]*>?/gm, ''); // strip HTML tags for preview
  } catch {
    return content.replace(/<[^>]*>?/gm, '');
  }
};
</script>

<template>
  <div class="flex items-center gap-3">
    <!-- Search Input -->
    <div class="relative flex-1">
      <Search class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
      <Input v-model="searchQuery" @input="onSearchInput" placeholder="Cari judul diskusi" class="pl-10" />
    </div>

    <!-- Lesson Filter Dropdown -->
    <Select v-model="selectedLessonId" @update:modelValue="updateFilters">
      <SelectTrigger class="w-[320px]">
        <SelectValue placeholder="Semua Materi" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="all">Semua Materi</SelectItem>
        <template v-for="courseItem in courses" :key="(courseItem as any).id">
          <template v-for="mod in (courseItem as any).modules ?? []" :key="mod.id">
            <SelectGroup>
              <SelectLabel>{{ (courseItem as any).title }} —
                {{ mod.title }}</SelectLabel>
              <SelectItem v-for="lesson in mod.lessons ?? []" :key="lesson.id" :value="String(lesson.id)">
                {{ lesson.title }}
              </SelectItem>
            </SelectGroup>
          </template>
        </template>
      </SelectContent>
    </Select>

    <!-- New Thread Button -->
    <Button @click="openNewThread" class="shrink-0">
      <Plus class="mr-1 h-4 w-4" />
      Buat Diskusi
    </Button>
  </div>

  <!-- Active Filter Chip -->
  <div v-if="selectedLessonLabel" class="flex items-center gap-2 text-sm">
    <span class="text-gray-500">Diskusi berdasarkan:</span>
    <span
      class="inline-flex items-center gap-1.5 rounded-full bg-gray-900 px-3 py-1 text-xs font-medium text-white dark:bg-gray-100 dark:text-gray-900">
      Materi: {{ selectedLessonLabel }}
      <button @click="clearLessonFilter"
        class="ml-1 rounded-full p-0.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-300">
        <X class="h-3 w-3" />
      </button>
    </span>
  </div>

  <!-- Thread List + Detail Split -->
  <div class="flex gap-4">
    <div :class="[selectedThread ? 'w-[400px]' : 'w-full', 'transition-all duration-300 shrink-0']">
      <ScrollArea class="h-[calc(100vh-320px)] border rounded-md">
        <div v-if="threads && threads.length === 0"
          class="flex flex-col items-center justify-center gap-3 rounded-xl py-16 text-center text-muted-foreground">
          <MessageSquare class="h-10 w-10 text-gray-300" />
          <p class="text-sm">Belum ada diskusi.</p>
          <Button variant="outline" size="sm" @click="newThreadOpen = true">
            <Plus class="mr-1 h-3.5 w-3.5" />
            Buat Diskusi Pertama
          </Button>
        </div>
        <div v-else-if="threads" class="flex flex-col">
          <button v-for="thread in threads" :key="thread.id" @click="selectThread(thread)" :class="[
            'p-4 text-left border-b hover:bg-muted transition-colors',
            selectedThread?.id === thread.id ? 'bg-muted' : 'bg-background'
          ]">
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="font-medium text-sm line-clamp-2">{{ (thread as any).title }}</h3>
              <span class="text-xs text-muted-foreground shrink-0">{{ new Date((thread as
                any).created_at).toLocaleDateString() }}</span>
            </div>
            <!-- Clean html tags for preview -->
            <div class="text-xs text-muted-foreground line-clamp-2 mb-3"
              v-html="renderPreview((thread as any).content)">
            </div>
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
              <span class="flex items-center gap-1">
                <MessageSquare class="w-3 h-3" />
                {{ (thread as any).replies_count || 0 }} balasan
              </span>
              <span>•</span>
              <span class="truncate">{{ (thread as any).lesson?.title }}</span>
            </div>
          </button>
        </div>
      </ScrollArea>
    </div>

    <!-- Detail -->
    <div v-if="selectedThread" class="flex-1 min-w-0 transition-all duration-300">
      <ScrollArea class="h-[calc(100vh-320px)] border rounded-md p-6 bg-background">
        <Thread :thread="selectedThread" @close="selectedThread = null" />
      </ScrollArea>
    </div>
  </div>

  <!-- New Thread Dialog -->
  <AddThreadDialog :course="course" :lessons="(course as any)?.modules?.flatMap((m: any) => m.lessons) || []"
    v-model:open="newThreadOpen" />
</template>