<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Bold } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle
    size="icon" 
    variant="editor"
    :model-value="editor.isActive('bold')"
    @mousedown.prevent
    @click="editor.chain().focus().toggleBold().run()"
  >
    <Bold />
  </Toggle>
</template>