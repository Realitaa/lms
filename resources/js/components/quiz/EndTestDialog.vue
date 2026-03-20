<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

const open = defineModel<boolean>('open');
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Akhiri Kuis</DialogTitle>
      </DialogHeader>
      <div class="flex flex-col gap-4">
        <p>Apakah anda yakin ingin mengakhiri kuis?</p>
        <div class="flex justify-end gap-2">
          <Button variant="outline" @click="open = false">Batal</Button>
          <Button variant="destructive" @click="open = false">Ya, Saya Yakin</Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
