<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Image } from 'lucide-vue-next'
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
const imageLink = ref('');

function setImage() {
  props.editor.chain().focus().setImage({ src: imageLink.value }).run();
  imageLink.value = '';
  open.value = false;
}
</script>

<template>
  <Dialog v-model:open="open">
    <form>
      <DialogTrigger as-child>
        <Button variant="ghost" class="h-8 w-8">
          <Image />
        </Button>
      </DialogTrigger>
      <DialogContent class="sm:max-w-106.25">
        <DialogHeader>
          <DialogTitle>Menyematkan Foto</DialogTitle>
          <DialogDescription>
            Upload atau tempelkan link foto untuk ditampilkan ke materi.
          </DialogDescription>
        </DialogHeader>
        <div class="flex justify-center items-center aspect-video rounded-xl border overflow-hidden">
          <img 
            v-if="imageLink" 
            :src="imageLink" 
            class="max-w-full max-h-full object-contain"
          >
          <p v-else class="w-50 text-center text-muted-foreground text-sm">
            Foto akan muncul disini jika dapat disematkan
          </p>
        </div>
        <div class="grid gap-4">
          <div class="grid gap-3">
            <Label for="picture">Upload gambar</Label>
            <Input id="picture" type="file" name="picture" />
          </div>
          <div class="grid gap-3">
            <Label for="link">Link Gambar</Label>
            <Input v-model="imageLink" id="link" name="link" placeholder="Link gambar yang dapat disematkan" />
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
            :disabled="imageLink == ''"
            @click="setImage"
          >
            Sematkan
          </Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>