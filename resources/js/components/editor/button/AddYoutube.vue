<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Youtube } from 'lucide-vue-next'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
  editor: Editor
}>()

const open = ref(false);
const videoLink = ref('');
const videoWidth = ref('640');
const videoHeight = ref('480');

function setImage() {
  props.editor.commands.setYoutubeVideo({
    src: videoLink.value,
    width: parseInt(videoWidth.value),
    height: parseInt(videoHeight.value),
  })
  videoLink.value = '';
  open.value = false;
}
</script>

<template>
  <Dialog v-model:open="open">
    <form>
      <DialogTrigger as-child>
        <Button variant="ghost" class="h-8 w-8">
          <Youtube />
        </Button>
      </DialogTrigger>
      <DialogContent class="sm:max-w-106.25">
        <DialogHeader>
          <DialogTitle>Menyematkan Video</DialogTitle>
          <DialogDescription>
            <!-- Upload atau tempelkan link foto untuk ditampilkan ke materi. -->
            Tempel link video Youtube untuk ditampilkan ke editor
          </DialogDescription>
        </DialogHeader>
        <div class="flex justify-center items-center aspect-video rounded-xl border overflow-hidden">
          <iframe
            v-if="videoLink" 
            :src="videoLink" 
            :width="videoWidth"
            :height="videoHeight"
            class="max-w-full max-h-full object-contain"
          ></iframe>
          <p v-else class="w-50 text-center text-muted-foreground text-sm">
            Video akan muncul disini jika dapat disematkan
          </p>
        </div>
        <div class="grid gap-4">
          <!-- <div class="grid gap-3">
            <Label for="picture">Upload video</Label>
            <Input id="picture" type="file" name="picture" />
          </div> -->
          <div class="grid gap-3">
            <Label for="link">Link Video Youtube</Label>
            <Input v-model="videoLink" id="link" name="link" placeholder="Link video Youtube (cth: )" />
          </div>
          <div class="gap-3 flex justify-between">
            <div class="grid gap-3">
              <Label for="videoWidth">Widht</Label>
              <Input v-model="videoWidth" id="videoWidth" name="videoWidth" placeholder="640" />
            </div>
            <div class="grid gap-3">
              <Label for="videoHeight">Height</Label>
              <Input v-model="videoHeight" id="videoHeight" name="videoHeight" placeholder="480" />
            </div>
          </div>
        </div>
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline">
              Batal
            </Button>
          </DialogClose>
          <Button 
            type="submit" 
            :disabled="videoLink == ''"
            @click="setImage"
          >
            Sematkan
          </Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>