<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Strikethrough } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle
    size="icon" 
    variant="editor"
    :model-value="editor.isActive('strike')"
    @click="editor.chain().focus().toggleStrike().run()"
  >
    <Strikethrough />
  </Toggle>
</template>