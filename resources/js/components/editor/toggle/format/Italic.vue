<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Italic } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle
    size="icon" 
    variant="editor"
    :model-value="editor.isActive('italic')"
    @mousedown.prevent
    @click="editor.chain().focus().toggleItalic().run()"
  >
    <Italic />
  </Toggle>
</template>