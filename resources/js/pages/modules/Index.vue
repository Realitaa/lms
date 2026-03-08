<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, ChevronDown, Pencil, Check, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import CourseForm from '@/components/courses/CourseForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/courses';
import type { BreadcrumbItem, Course } from '@/types';
import TitleWithBack from '@/components/TitleWithBack.vue';
import { Button } from '@/components/ui/button'
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area'
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import {
  RadioGroup,
  RadioGroupItem,
} from '@/components/ui/radio-group'

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

const isSubmitting = ref(false);

function onSubmit(formData: FormData) {
  formData.append('_method', 'PUT');
  isSubmitting.value = true;
  router.post(update.url(props.course.id), formData, {
    onSuccess: () => {
      toast.success('Kursus berhasil diperbarui');
    },
    onError: (errors) => {
      toast.error(`Gagal memperbarui kursus: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
}

const display = ref('test');
const isControl = ref(true);

const tags = Array.from({ length: 50 }).map(
  (_, i, a) => `v1.2.0-beta.${a.length - i}`,
)

function displayControl() {
  display.value = 'control'
  setTimeout(() => {
    isControl.value = true;
  }, 300);
}

function displayTest() {
  isControl.value = false;
  // loading simulation, also create UI to show loading state
  document.body.style.cursor = "wait";
  setTimeout(() => {
    display.value = 'test'
    document.body.style.cursor = "default";
  }, 500);
}

function displayModule() {
  isControl.value = false;
  // loading simulation, also create UI to show loading state
  document.body.style.cursor = "wait";
  setTimeout(() => {
    display.value = 'module'
    document.body.style.cursor = "default";
  }, 500);
}
</script>

<template>

  <Head title="Edit Kursus" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <TitleWithBack
        back-url="/courses"
        :title="`Manajemen Modul ${course.title}`"
        :subtitle="`Kelola modul dan materi pada kursus ${course.title}`"
      />

    </div>

    <div class="flex justify-end gap-4 mx-4 h-[calc(100vh-200px)]">

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

      <Transition name="fade">
        <div class="w-[70%] transition-opacity duration-100" v-if="display == 'test'">
          <ScrollArea class="w-full pb-3 px-2">
            <div class="flex w-max gap-2">
              <!-- Button to change soal -->
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
                  <!-- Question previewed real time when editing -->
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Enim corrupti autem illo at? Praesentium, eaque. Minus repudiandae odio veniam consequuntur maxime, sed nesciunt beatae eius mollitia quisquam blanditiis sequi voluptatibus.</p>
                  <!-- Button to edit question -->
                  <Button size="icon" variant="ghost" >
                    <Pencil />
                  </Button>
                  <!-- Button to save edited question -->
                  <Button size="icon" variant="ghost">
                    <Check />
                    <!-- Button to cancel edit question -->
                  </Button>
                  <Button size="icon" variant="ghost">
                    <X />
                  </Button>
                </div>
                  <!-- Option previewed real time when editing -->
                <RadioGroup default-value="comfortable">
                  <div class="flex items-center space-x-2" v-for="i in 10" :key="i">
                    <RadioGroupItem :id="`r${i}`" value="default" />
                    <Label for="r1">Default</Label>
                    <!-- Button to edit option -->
                    <Button size="icon" variant="ghost">
                      <Pencil />
                    </Button>
                    <!-- Button to save edited option -->
                    <Button size="icon" variant="ghost">
                      <Check />
                    </Button>
                    <!-- Button to cancel edited option -->
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
            <!-- show editor with transition when user want to edit question or options -->
            <div class="h-1/2">
              <RichTextEditor :config="[['undoRedo'], ['bold', 'italic', 'underline', 'strike']]" />
            </div>
          </div>
        </div>
      </Transition>

      <Card class="h-full flex flex-col p-4 transition-all duration-500" :class="isControl ? 'w-full' : 'w-2/7'">
        <CardHeader class="px-0">
          <CardTitle>Modul dan Materi</CardTitle>
          <CardDescription>
            <span v-if="isControl">Drag & Drop untuk reposisi materi atau modul. Tombol + disamping untuk menambah modul.</span>
            <span v-else>Tombol pensil untuk mengatur modul</span>
          </CardDescription>
          <CardAction>
            <Button variant="ghost" v-if="isControl">
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
              <template v-for="tag in tags" :key="tag">
                <Card class="w-full mb-2 p-4 gap-2!">
                  <CardHeader class="px-0 pb-2! border-b">
                    <CardTitle>Pengenalan Komputer dan Penggunaan Singkat</CardTitle>
                    <CardDescription>
                      Deskripsi modul
                    </CardDescription>
                    <CardAction>
                      <Button size="icon" variant="ghost">
                        <ChevronDown />
                      </Button>
                    </CardAction>
                  </CardHeader>
                  <CardContent class="px-0">
                    <div v-for="i in 5" :key="i" class="flex justify-between border-b py-2">
                      <p class="font-bold text-sm hover:underline hover:cursor-pointer" @click="displayModule">Apa itu BIOS (Basic Input Output System)?</p>
                    </div>
                    <div class="flex justify-between border-b py-2">
                      <p class="font-bold text-sm hover:underline hover:cursor-pointer" @click="displayTest">Uji Pemahaman</p>
                    </div>
                  </CardContent>
                </Card>
              </template>
            <ScrollBar orientation="vertical" />
          </ScrollArea>
        </CardContent>
      </Card>
    </div>

  </AppLayout>
</template>
