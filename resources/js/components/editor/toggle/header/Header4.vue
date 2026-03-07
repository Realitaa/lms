<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading4 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 4 })"
    @click="editor.chain().focus().toggleHeading({ level: 4 }).run()">
    <Heading4 />
  </Toggle>
</template>