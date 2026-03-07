<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Underline } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle
    size="icon" 
    variant="editor"
    :model-value="editor.isActive('underline')"
    @mousedown.prevent
    @click="editor.chain().focus().toggleUnderline().run()"
  >
    <Underline />
  </Toggle>
</template>