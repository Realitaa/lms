<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Sigma } from 'lucide-vue-next'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import MathDialog from '../MathDialog.vue'

const props = defineProps<{
  editor: Editor
}>()

const open = ref(false)
</script>

<template>
  <div>
    <Button 
      variant="ghost" 
      class="h-8 w-8 p-0" 
      @click="open = true"
      title="Tambah Rumus Matematika"
    >
      <Sigma class="w-4 h-4" />
    </Button>

    <MathDialog 
      v-model:open="open" 
      :editor="editor" 
    />
  </div>
</template>