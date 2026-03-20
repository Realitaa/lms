<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

import type { BreadcrumbItem } from '@/types';
import Title from '@/components/Title.vue';
import Discussion from '@/components/discussion/Discussion.vue';

import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from '@/components/ui/drawer'
import { Button } from '@/components/ui/button';
import student from '@/routes/student';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import LessonLists from '@/components/learning/LessonLists.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Kursus Saya',
    href: student.index().url,
  },
  {
    title: 'Nama Kursus',
    href: '#',
  },
];

const openDiscussion = ref(false);
</script>

<template>

  <Head title="Belajar" />

  <AppLayout :breadcrumbs="breadcrumbs" class="bg-sky-100 dark:bg-black">
    <div class="p-4 flex flex-col gap-4">
      <div
        class="space-y-2 flex justify-between items-center p-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm">
        <div class="space-y-2">
          <Title title="Nama Kursus"
            subtitle="Silahkan selesaikan kelas dan meluluskan kuis pada materi agar dapat melanjutkan ke materi selanjutnya." />
          <p class="text-sm text-muted-foreground">24 Materi</p>
        </div>
        <!-- Button to change between course and discussion forum -->
        <Button variant="outline" @click="openDiscussion = !openDiscussion">{{ openDiscussion ? 'Kembali ke Materi' :
          'Forum Diskusi' }}</Button>
      </div>

      <div v-if="!openDiscussion" class="flex flex-col lg:flex-row w-full gap-4">

        <!-- Main Content (Readable width) -->
        <div
          class="space-y-2 py-4 pl-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm w-full max-w-[750px] mx-auto xl:mx-0">
          <ScrollArea class="h-[calc(100vh-280px)] pr-4">
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Odit inventore ab nostrum eligendi numquam vel
            nemo
            officiis dolorem earum, amet est non, maxime reprehenderit. Sint magnam fugit possimus, odit eaque nulla
            animi
            atque qui in voluptates reprehenderit, dolore laudantium architecto recusandae voluptatem error iste
            repudiandae non deserunt consequuntur consectetur harum molestiae officia placeat? Ullam laboriosam impedit
            sint earum! Est, eos nisi sed magni harum suscipit, delectus reiciendis ipsa cumque porro obcaecati aliquid
            officia doloribus ad iusto, nihil quidem iure asperiores. Obcaecati voluptatum iure enim soluta totam
            doloremque labore natus quis fugit quos inventore, tenetur distinctio eligendi error rerum necessitatibus
            sed
            explicabo fuga illum quas. Saepe unde in voluptate dolore harum, odio similique consequuntur ipsa sapiente
            rerum neque accusamus earum, quidem quas dolorum fugiat laudantium officiis veniam minus eaque porro?
            Nesciunt, praesentium quo, quas, possimus minima nihil eaque excepturi tenetur eligendi distinctio sed.
            Fugit
            ducimus ipsa quas blanditiis animi, ex sint, sit quaerat asperiores aliquam quisquam! Minima esse sed quos
            dolores maiores voluptas, quae excepturi nobis, nulla blanditiis ipsum! Eaque repellat labore, facilis
            dolore
            at ratione neque iusto fuga? Magni praesentium, consequatur consequuntur quis minus tenetur. Consectetur
            ducimus iste possimus consequatur qui suscipit numquam consequuntur aperiam! A, eligendi. Blanditiis laborum
            dolor nemo ut. Atque suscipit tempora ad nisi doloremque. Aperiam nam sint iusto corrupti sapiente eligendi
            debitis tempore, esse eos aliquid et voluptatum reprehenderit laudantium fugiat, earum animi neque. Illum
            dolor repellendus hic nostrum. Pariatur dolorem ad repellendus molestiae minus, dolores eaque veniam velit
            nam
            totam doloremque mollitia id esse ducimus natus blanditiis quam sed ab in accusamus tenetur fugiat, vel
            deleniti magnam. Accusantium dolorem temporibus molestiae, facere rem recusandae deleniti deserunt
            doloremque,
            minus aperiam commodi iusto totam distinctio ratione quas suscipit maiores blanditiis quasi porro sit sunt.
            Ducimus, natus illum alias praesentium pariatur voluptatem aliquam distinctio laboriosam nostrum amet qui
            saepe, accusantium quam? Dolorum quos sit eveniet aliquid. Numquam atque totam consequuntur sed architecto.
            Quasi excepturi amet vitae modi, blanditiis incidunt eum delectus fuga necessitatibus odio dignissimos, nam
            cumque. Ipsum quasi deserunt, ipsa rem sint amet eveniet itaque minima consectetur officiis error odit quas
            porro minus est maiores recusandae perferendis, nostrum quae, fuga quaerat? Nisi dignissimos quisquam libero
            enim in commodi nesciunt, exercitationem, praesentium necessitatibus minus nostrum eaque aperiam, ullam
            cumque
            excepturi. Vel neque est magni aspernatur dignissimos, animi sint voluptates quod aliquam eius fugiat
            nesciunt
            voluptas sit mollitia perferendis laudantium adipisci sapiente quasi accusantium minus. Pariatur itaque sint
            ipsa delectus dolorum obcaecati, tempora cum velit voluptas, quos optio sequi sapiente neque aliquid
            voluptate
            doloremque dicta, a laudantium inventore quia sunt qui blanditiis suscipit numquam. Cupiditate illum nam
            facilis quibusdam harum facere temporibus praesentium soluta, optio enim distinctio quia at numquam itaque
            accusantium cumque culpa eaque! Dolor tempore odit inventore non itaque facere velit doloribus nulla
            corporis
            similique sit blanditiis, impedit, tempora rem illum iusto, id provident atque alias obcaecati laborum sequi
            odio eius. Rem, tenetur vel. Itaque accusamus architecto quia rem! Inventore maxime, rem sint assumenda
            nihil
            praesentium fugit quas omnis accusantium ducimus iusto voluptatibus consequatur reprehenderit corporis
            delectus ipsum, quod neque at expedita error! Iusto voluptatum, natus, veniam et voluptatem magnam sit
            accusantium aut consequatur at dolorem doloremque velit similique quas voluptatibus eveniet officiis quasi
            porro. Aliquam, aspernatur deserunt dignissimos voluptatibus eligendi provident non, magnam sed libero
            ducimus
            asperiores tempora vero iusto, ratione inventore facilis maxime architecto adipisci repudiandae
            necessitatibus
            doloribus quisquam. Modi consequatur esse nulla sed, id blanditiis explicabo eligendi in natus? Illum quis
            explicabo tenetur eligendi est amet, id vitae similique, aut ratione soluta ipsa nam quibusdam rerum quos
            optio illo exercitationem tempore? Corrupti quos dignissimos eius repellat ducimus quidem et, libero
            quisquam
            sed ipsam provident laboriosam aliquam sunt asperiores soluta deleniti modi accusamus iste in qui
            consequatur
            aperiam maxime. Perspiciatis quam amet corporis corrupti animi sed similique sunt libero explicabo
            voluptatum
            qui deleniti itaque ratione, optio eum iste fugit voluptatem vitae cupiditate vero? Dolorem ratione nostrum
            molestias hic quos, aliquid, sapiente commodi nulla dolores odit quasi officiis rem harum nam alias
            obcaecati
            quod reiciendis amet tempore aperiam tenetur, minus ex itaque doloribus! In asperiores, quidem eos saepe
            impedit repellendus nihil quisquam magnam reiciendis soluta quibusdam maxime esse tempora laborum quas rem
            minus dolorem! Consequatur, modi nemo? Fugit maxime esse, modi, ex ullam eum, expedita odit doloremque
            laudantium distinctio nam non saepe minima? Tempora necessitatibus enim reiciendis quae debitis qui ex
            sapiente voluptas commodi? Id dicta neque rem nam, magni aspernatur velit tempore, voluptatem ea nihil
            nesciunt sequi deleniti, explicabo quod architecto possimus eos officia laudantium. Quaerat mollitia
            doloribus
            laborum repellendus ea, aliquid hic reprehenderit ipsa, sit laudantium cupiditate? Ipsa quod debitis
            pariatur.
            Ratione ullam aliquid esse excepturi explicabo assumenda vel, sit corporis hic accusantium unde id
            perferendis
            ab nihil aut alias voluptatibus illo quidem cupiditate incidunt suscipit est? Provident deleniti natus iusto
            esse. Distinctio sit eligendi temporibus et earum laborum sed rerum totam? Doloribus hic facere ullam
            accusamus, vel optio, quibusdam officia nobis sunt tempora laudantium possimus eum dicta aut praesentium nam
            sit beatae quos voluptates delectus soluta non? Commodi unde voluptatum voluptatibus perspiciatis maxime ad
            molestiae et nemo saepe. Fuga reiciendis quia laboriosam, error ullam cupiditate totam blanditiis
            praesentium
            eaque amet quos sint minima harum cum nisi adipisci dolores facere labore magni, assumenda, nemo deserunt
            pariatur ea. Veritatis molestias reiciendis at porro! Voluptatum praesentium blanditiis nihil suscipit
            fugiat
            ad! Animi consequuntur eaque porro, veritatis nisi impedit et, sint expedita quibusdam doloribus ipsam
            numquam
            amet magnam cumque quo fugiat beatae? Reiciendis vitae voluptatum doloremque modi? Dicta doloribus, nisi,
            rem
            error distinctio quidem animi dolores sunt mollitia suscipit eos. Tempora dolorem accusamus perferendis
            eius,
            mollitia quaerat soluta itaque eos dolore nemo ipsa laborum modi consequatur ratione ea, cupiditate natus
            error aperiam veniam explicabo dolores eum harum magni praesentium! Blanditiis fugiat consectetur,
            exercitationem ratione earum sed praesentium minima culpa nulla sit dolorum maxime est esse. Consequuntur,
            quaerat. Praesentium dolor saepe blanditiis excepturi quidem veritatis fugit tempore. Incidunt, voluptates.
            Dolor earum porro tenetur fugiat accusamus optio tempore corporis quod repellat. Cumque, eaque. Quos ut
            ducimus dignissimos alias expedita modi soluta corrupti assumenda debitis sunt, hic exercitationem ipsam
            saepe.

            <div class="flex mt-4 justify-between items-center border rounded-2xl p-4">
              <p>Selesaikan kuis dengan nilai minimal 80 untuk melanjutkan ke materi selanjutnya.</p>
              <Button>Mulai Kuis</Button>
            </div>
          </ScrollArea>
        </div>

        <!-- Sidebar (takes remaining space) -->
        <div
          class="space-y-2 py-4 pl-4 border rounded-2xl bg-white dark:bg-transparent shadow-sm w-full hidden xl:block xl:flex-1">
          <h2 class="text-lg font-medium">Materi Belajar</h2>
          <LessonLists />
        </div>

        <div class="xl:hidden">
          <Drawer>
            <DrawerTrigger as-child>
              <div class="w-full absolute bottom-0 left-0 right-0 bg-white">
                <Button class="w-full" variant="outline">Buka Materi</Button>
              </div>
            </DrawerTrigger>
            <DrawerContent>
              <DrawerHeader>
                <DrawerTitle>Materi Belajar</DrawerTitle>
                <DrawerDescription>
                  <LessonLists />
                </DrawerDescription>
              </DrawerHeader>
              <DrawerFooter>
                <DrawerClose as-child>
                  <Button variant="outline">Tutup</Button>
                </DrawerClose>
              </DrawerFooter>
            </DrawerContent>
          </Drawer>
        </div>

      </div>

      <div v-else class="flex flex-col lg:flex-row w-full gap-4">
        <!-- <Discussion :course="course" :courses="courses" :threads="threads" :filters="filters" /> -->
      </div>
    </div>
  </AppLayout>
</template>