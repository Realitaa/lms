<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading3 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 3 })"
    @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">
    <Heading3 />
  </Toggle>
</template>