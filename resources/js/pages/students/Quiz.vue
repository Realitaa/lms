<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref, Teleport } from 'vue';
import { SquareArrowRightExit, ChevronLeft, ChevronRight, CircleQuestionMark } from 'lucide-vue-next';
import MaterialSymbolsWindow from '@/components/icons/MaterialSymbolsWindow.vue';
import Title from '@/components/Title.vue';
import { Button } from '@/components/ui/button';
import QuestionListDialog from '@/components/quiz/QuestionListDialog.vue';
import QuestionList from '@/components/quiz/QuestionList.vue';
import EndTestDialog from '@/components/quiz/EndTestDialog.vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';

const { auth } = usePage().props;
const userName = auth.user.name;
const time = ref('00:00:00');
const questionListOpen = ref(false);
const endTestOpen = ref(false);
</script>

<template>

  <Head title="Belajar" />

  <Teleport to="body">
    <div class="w-full h-[100vh] bg-sky-100 dark:bg-black">
      <div class="p-4 flex flex-col gap-4">
        <div
          class="space-y-2 flex flex-col md:flex-row justify-between md:items-center p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
          <div class="space-y-2">
            <Title title="Nama Kursus"
              :subtitle="`Selesaikan kursus dengan minimal nilai ${80} untuk melanjutkan ke materi selanjutnya.`" />
          </div>
          <div class="flex items-center gap-2 text-nowrap justify-between md:justify-end">
            <div class="flex flex-col md:items-end">
              <p class="font-semibold">{{ userName }}</p>
              <p class="text-sm text-muted-foreground">Waktu tersisa: <span class="tabular-nums">{{ time }}</span></p>
            </div>
            <div>
              <Button variant="ghost" size="icon" class="lg:hidden cursor-pointer" @click="questionListOpen = true">
                <MaterialSymbolsWindow class="w-4 h-4" />
              </Button>
              <Button variant="ghost" size="icon" class="text-red-500 hover:text-red-600 cursor-pointer"
                @click="endTestOpen = true">
                <SquareArrowRightExit class="w-4 h-4" />
              </Button>
            </div>
          </div>
        </div>

        <div class="flex gap-4">
          <div class="w-full">
            <div class="p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
              <h2 class="text-xl font-semibold ml-4">Soal 1</h2>
              <div class="px-4">
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Corrupti quam quasi explicabo autem amet iure
                reprehenderit libero harum, voluptates accusamus. Sit soluta perspiciatis non eaque nobis nesciunt, qui
                unde. Ipsam vero qui voluptate excepturi iste, consequuntur veniam rerum dolore, non itaque minima
                dolores
                tempora optio nisi voluptatibus ad officiis culpa aspernatur reprehenderit, tenetur expedita dolorem eum
                doloremque sint! Nisi, dignissimos pariatur? Excepturi eius iusto debitis similique, accusamus ducimus
                voluptates dolores ea temporibus dicta repellat, ullam illo ipsum porro fugiat, odio aspernatur rerum
                aliquid. Facere consequatur delectus voluptate voluptates, eaque error similique, soluta ab repellat,
                commodi suscipit voluptatum accusantium itaque! Totam!
              </div>
              <RadioGroup default-value="comfortable" class="p-4">
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r1" value="default" />
                  <Label for="r1">Default</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r2" value="comfortable" />
                  <Label for="r2">Comfortable</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r3" value="compact" />
                  <Label for="r3">Compact</Label>
                </div>
              </RadioGroup>
              <div class="flex justify-between mt-4 px-4">
                <Button variant="outline" class="cursor-pointer">
                  <ChevronLeft class="w-4 h-4" />
                  Sebelumnya
                </Button>
                <Button variant="outline" class="cursor-pointer">
                  <CircleQuestionMark class="w-4 h-4" />
                  Ragu-Ragu
                </Button>
                <Button variant="outline" class="cursor-pointer">
                  Selanjutnya
                  <ChevronRight class="w-4 h-4" />
                </Button>
              </div>
            </div>
          </div>
          <div class="p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm max-w-150 hidden lg:block">
            <h2 class="text-xl font-semibold ml-4">Daftar Soal</h2>
            <QuestionList />
          </div>
        </div>
      </div>
    </div>
    <QuestionListDialog v-model:open="questionListOpen" />
    <EndTestDialog v-model:open="endTestOpen" />
  </Teleport>
</template>