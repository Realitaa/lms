<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus, ChevronDown, ChevronUp, Pencil, Check, X, Trash2, GripVertical } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import { VueDraggable } from 'vue-draggable-plus';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Course, Module, Lesson } from '@/types';
import TitleWithBack from '@/components/TitleWithBack.vue';
import AddModuleDialog from '@/components/modules/AddModuleDialog.vue';
import AddLessonDialog from '@/components/modules/AddLessonDialog.vue';
import DeleteLessonDialog from '@/components/modules/DeleteLessonDialog.vue';
import DeleteModuleDialog from '@/components/modules/DeleteModuleDialog.vue';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs';
import {
  RadioGroup,
  RadioGroupItem,
} from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';

const props = defineProps<{
  course: Course;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Kursus',
    href: '/courses',
  },
  {
    title: props.course.title,
    href: `/courses/${props.course.id}`,
  },
  {
    title: 'Manajemen Modul',
    href: '#',
  },
];

// --- Display state ---
const display = ref('control');
const isControl = ref(true);

function displayControl() {
  display.value = 'control';
  setTimeout(() => {
    isControl.value = true;
  }, 300);
}

function displayTest() {
  isControl.value = false;
  document.body.style.cursor = 'wait';
  setTimeout(() => {
    display.value = 'test';
    document.body.style.cursor = 'default';
  }, 500);
}

function displayModule() {
  isControl.value = false;
  document.body.style.cursor = 'wait';
  setTimeout(() => {
    display.value = 'module';
    document.body.style.cursor = 'default';
  }, 500);
}

// --- Modules data (reactive copy from props) ---
const modules = computed(() => props.course.modules ?? []);

// --- Collapsed state per module ---
const collapsedModules = ref<Record<number, boolean>>({});

function toggleCollapse(moduleId: number) {
  collapsedModules.value[moduleId] = !collapsedModules.value[moduleId];
}

// --- Add Module Dialog ---
const addModuleDialogOpen = ref(false);

// --- Add Lesson Dialog ---
const addLessonDialogOpen = ref(false);
const addLessonModuleId = ref<number | null>(null);

function openAddLessonDialog(moduleId: number) {
  addLessonModuleId.value = moduleId;
  addLessonDialogOpen.value = true;
}

// --- Delete Lesson Dialog ---
const deleteLessonDialogOpen = ref(false);
const selectedLesson = ref<Lesson | null>(null);

function openDeleteLessonDialog(lesson: Lesson) {
  selectedLesson.value = lesson;
  deleteLessonDialogOpen.value = true;
}

// --- Delete Module Dialog ---
const deleteModuleDialogOpen = ref(false);
const selectedModule = ref<Module | null>(null);

function openDeleteModuleDialog(mod: Module) {
  selectedModule.value = mod;
  deleteModuleDialogOpen.value = true;
}

// --- Inline edit module name ---
const editingModuleId = ref<number | null>(null);
const editingModuleTitle = ref('');

function startEditModule(mod: Module) {
  editingModuleId.value = mod.id;
  editingModuleTitle.value = mod.title;
}

function cancelEditModule() {
  editingModuleId.value = null;
  editingModuleTitle.value = '';
}

function saveEditModule(mod: Module) {
  if (!editingModuleTitle.value.trim()) return;

  router.put(`/courses/${props.course.id}/modules/${mod.id}`, {
    title: editingModuleTitle.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Nama modul berhasil diperbarui');
      cancelEditModule();
    },
    onError: (errors) => {
      toast.error(`Gagal memperbarui modul: ${Object.values(errors)[0]}`);
    },
  });
}

// --- Inline edit lesson name ---
const editingLessonId = ref<number | null>(null);
const editingLessonTitle = ref('');

function startEditLesson(lesson: Lesson) {
  editingLessonId.value = lesson.id;
  editingLessonTitle.value = lesson.title;
}

function cancelEditLesson() {
  editingLessonId.value = null;
  editingLessonTitle.value = '';
}

function saveEditLesson(lesson: Lesson) {
  if (!editingLessonTitle.value.trim()) return;

  router.put(`/courses/lessons/${lesson.id}`, {
    title: editingLessonTitle.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Nama materi berhasil diperbarui');
      cancelEditLesson();
    },
    onError: (errors) => {
      toast.error(`Gagal memperbarui materi: ${Object.values(errors)[0]}`);
    },
  });
}

// --- Drag & Drop: Reorder modules ---
function onModuleDragEnd() {
  const reordered = modules.value.map((mod, idx) => ({
    id: mod.id,
    order: idx,
  }));

  router.post(`/courses/${props.course.id}/modules/reorder`, {
    modules: reordered,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Urutan modul berhasil diperbarui');
    },
    onError: () => {
      toast.error('Gagal memperbarui urutan modul');
    },
  });
}

// --- Drag & Drop: Reorder lessons within module ---
function onLessonDragEnd(mod: Module) {
  const reordered = mod.lessons.map((lesson, idx) => ({
    id: lesson.id,
    order: idx,
    module_id: mod.id,
  }));

  router.post(`/courses/${props.course.id}/lessons/reorder`, {
    lessons: reordered,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Urutan materi berhasil diperbarui');
    },
    onError: () => {
      toast.error('Gagal memperbarui urutan materi');
    },
  });
}
</script>

<template>

  <Head title="Manajemen Modul" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <TitleWithBack back-url="/courses" :title="`Manajemen Modul ${course.title}`"
        :subtitle="`Kelola modul dan materi pada kursus ${course.title}`" />
    </div>

    <div class="flex justify-end gap-4 mx-4 h-[calc(100vh-200px)]">

      <!-- Module content view (70% width) -->
      <div class="w-[70%]" v-if="display == 'module'">
        <Tabs default-value="preview">
          <TabsList>
            <TabsTrigger value="editor">
              Editor
            </TabsTrigger>
            <TabsTrigger value="preview">
              Preview
            </TabsTrigger>
          </TabsList>
          <TabsContent value="editor">
            <RichTextEditor />
          </TabsContent>
          <TabsContent value="preview">
            <!-- preview of module -->
          </TabsContent>
        </Tabs>
      </div>

      <!-- Quiz view (70% width) -->
      <Transition name="fade">
        <div class="w-[70%] transition-opacity duration-100" v-if="display == 'test'">
          <ScrollArea class="w-full pb-3 px-2">
            <div class="flex w-max gap-2">
              <Button v-for="i in 30" :key="i">{{ i }}</Button>
              <Button variant="ghost">
                <Plus />
                Tambah Soal
              </Button>
            </div>
            <ScrollBar orientation="horizontal" class="bg-white rounded-2xl mx-2" />
          </ScrollArea>
          <div class="mt-2 flex flex-col h-[calc(100vh-255px)] space-y-2">
            <div class="h-1/2 py-2 pl-2 space-y-2 border rounded-2xl">
              <ScrollArea class="h-full overflow-auto">
                <div class="flex justify-between">
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit.</p>
                  <Button size="icon" variant="ghost">
                    <Pencil />
                  </Button>
                  <Button size="icon" variant="ghost">
                    <Check />
                  </Button>
                  <Button size="icon" variant="ghost">
                    <X />
                  </Button>
                </div>
                <RadioGroup default-value="comfortable">
                  <div class="flex items-center space-x-2" v-for="i in 10" :key="i">
                    <RadioGroupItem :id="`r${i}`" value="default" />
                    <Label :for="`r${i}`">Default</Label>
                    <Button size="icon" variant="ghost">
                      <Pencil />
                    </Button>
                    <Button size="icon" variant="ghost">
                      <Check />
                    </Button>
                    <Button size="icon" variant="ghost">
                      <X />
                    </Button>
                  </div>
                </RadioGroup>
                <Button variant="link" class="p-0!">
                  <Plus />
                  Tambah Opsi
                </Button>
              </ScrollArea>
            </div>
            <div class="h-1/2">
              <RichTextEditor :config="[['undoRedo'], ['bold', 'italic', 'underline', 'strike']]" />
            </div>
          </div>
        </div>
      </Transition>

      <!-- Module & Lesson Control Card -->
      <Card class="h-full flex flex-col p-4 transition-all duration-500" :class="isControl ? 'w-full' : 'w-2/7'">
        <CardHeader class="px-0">
          <CardTitle>Modul dan Materi</CardTitle>
          <CardDescription>
            <span v-if="isControl">Drag & Drop untuk reposisi materi atau modul. Tombol + disamping untuk menambah
              modul.</span>
            <span v-else>Tombol pensil untuk mengatur modul</span>
          </CardDescription>
          <CardAction>
            <Button variant="ghost" v-if="isControl" @click="addModuleDialogOpen = true">
              <Plus />
              Tambah Modul
            </Button>
            <Button size="icon" variant="ghost" v-else @click="displayControl">
              <Pencil />
            </Button>
          </CardAction>
        </CardHeader>
        <CardContent class="flex-1 overflow-hidden px-0">
          <ScrollArea class="h-full pr-3">

            <!-- Draggable module list (control mode) -->
            <VueDraggable v-if="isControl" v-model="course.modules" :animation="200" handle=".module-drag-handle"
              group="modules" @end="onModuleDragEnd" class="space-y-2">
              <Card v-for="mod in modules" :key="mod.id" class="w-full p-4 gap-2!">
                <CardHeader class="px-0 pb-2! border-b">
                  <!-- Module title: inline editable -->
                  <div class="flex items-center gap-2 flex-1">
                    <GripVertical class="h-4 w-4 shrink-0 cursor-grab text-muted-foreground module-drag-handle" />

                    <template v-if="editingModuleId === mod.id">
                      <Input v-model="editingModuleTitle" class="h-8 text-sm" @keyup.enter="saveEditModule(mod)"
                        @keyup.escape="cancelEditModule" autofocus />
                      <Button size="icon" variant="ghost" class="shrink-0" @click="saveEditModule(mod)">
                        <Check class="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" class="shrink-0" @click="cancelEditModule">
                        <X class="h-4 w-4" />
                      </Button>
                    </template>

                    <template v-else>
                      <CardTitle class="text-sm">{{ mod.title }}</CardTitle>
                    </template>
                  </div>

                  <CardAction class="flex items-center gap-1">
                    <template v-if="editingModuleId !== mod.id">
                      <Button size="icon" variant="ghost" @click="startEditModule(mod)">
                        <Pencil class="h-3.5 w-3.5" />
                      </Button>
                      <Button size="icon" variant="ghost" @click="openDeleteModuleDialog(mod)">
                        <Trash2 class="h-3.5 w-3.5 text-destructive" />
                      </Button>
                      <Button size="icon" variant="ghost" @click="toggleCollapse(mod.id)">
                        <ChevronDown v-if="!collapsedModules[mod.id]" class="h-4 w-4" />
                        <ChevronUp v-else class="h-4 w-4" />
                      </Button>
                    </template>
                  </CardAction>
                </CardHeader>

                <CardContent class="px-0" v-show="!collapsedModules[mod.id]">
                  <!-- Draggable lesson list -->
                  <VueDraggable v-model="mod.lessons" :animation="200" handle=".lesson-drag-handle"
                    :group="{ name: 'lessons' }" @end="onLessonDragEnd(mod)" class="min-h-[2rem]">
                    <div v-for="lesson in mod.lessons" :key="lesson.id"
                      class="flex items-center justify-between border-b py-2 gap-2">
                      <GripVertical class="h-3.5 w-3.5 shrink-0 cursor-grab text-muted-foreground lesson-drag-handle" />

                      <!-- Inline edit lesson -->
                      <template v-if="editingLessonId === lesson.id">
                        <Input v-model="editingLessonTitle" class="h-7 text-sm flex-1"
                          @keyup.enter="saveEditLesson(lesson)" @keyup.escape="cancelEditLesson" autofocus />
                        <Button size="icon" variant="ghost" class="shrink-0 h-7 w-7" @click="saveEditLesson(lesson)">
                          <Check class="h-3.5 w-3.5" />
                        </Button>
                        <Button size="icon" variant="ghost" class="shrink-0 h-7 w-7" @click="cancelEditLesson">
                          <X class="h-3.5 w-3.5" />
                        </Button>
                      </template>

                      <template v-else>
                        <p class="font-bold text-sm flex-1 hover:underline hover:cursor-pointer" @click="displayModule(lesson.id)">{{ lesson.title }}</p>
                        <div class="flex items-center gap-0.5 shrink-0">
                          <Button size="icon" variant="ghost" class="h-7 w-7" @click="startEditLesson(lesson)">
                            <Pencil class="h-3 w-3" />
                          </Button>
                          <Button size="icon" variant="ghost" class="h-7 w-7" @click="openDeleteLessonDialog(lesson)">
                            <Trash2 class="h-3 w-3 text-destructive" />
                          </Button>
                        </div>
                      </template>
                    </div>
                  </VueDraggable>

                  <!-- Add lesson button -->
                  <div class="flex gap-4">
                    <Button variant="link" class="p-0! mt-2 text-xs" @click="openAddLessonDialog(mod.id)">
                      <Plus class="h-3.5 w-3.5" />
                      Tambah Materi
                    </Button>
                    <!-- Add lesson button -->
                    <Button variant="link" class="p-0! mt-2 text-xs" @click="openAddQuizDialog(mod.id)">
                      <Plus class="h-3.5 w-3.5" />
                      Tambah Quiz
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </VueDraggable>

            <!-- Read-only module list (non-control mode) -->
            <template v-else>
              <template v-for="mod in modules" :key="mod.id">
                <Card class="w-full mb-2 p-4 gap-2!">
                  <CardHeader class="px-0 pb-2! border-b">
                    <CardTitle>{{ mod.title }}</CardTitle>
                    <CardAction>
                      <Button size="icon" variant="ghost" @click="toggleCollapse(mod.id)">
                        <ChevronDown v-if="!collapsedModules[mod.id]" />
                        <ChevronUp v-else />
                      </Button>
                    </CardAction>
                  </CardHeader>
                  <CardContent class="px-0" v-show="!collapsedModules[mod.id]">
                    <div v-for="lesson in mod.lessons" :key="lesson.id" class="flex justify-between border-b py-2">
                      <p class="font-bold text-sm hover:underline hover:cursor-pointer" @click="displayModule">{{
                        lesson.title }}</p>
                    </div>
                  </CardContent>
                </Card>
              </template>
            </template>

            <!-- Empty state -->
            <div v-if="modules.length === 0"
              class="flex flex-col items-center justify-center gap-3 py-12 text-center text-muted-foreground">
              <p class="text-sm">Belum ada modul.</p>
              <Button variant="outline" size="sm" @click="addModuleDialogOpen = true" v-if="isControl">
                <Plus class="mr-1 h-3.5 w-3.5" />
                Tambah Modul Pertama
              </Button>
            </div>

            <ScrollBar orientation="vertical" />
          </ScrollArea>
        </CardContent>
      </Card>
    </div>

    <!-- Dialogs -->
    <AddModuleDialog v-model:open="addModuleDialogOpen" :course-id="course.id" />
    <AddLessonDialog v-model:open="addLessonDialogOpen" :module-id="addLessonModuleId" />
    <DeleteLessonDialog v-model:open="deleteLessonDialogOpen" :lesson="selectedLesson" />
    <DeleteModuleDialog v-model:open="deleteModuleDialogOpen" :module="selectedModule" :course-id="course.id" />

  </AppLayout>
</template>
