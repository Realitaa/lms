<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading2 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 2 })"
    @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">
    <Heading2 />
  </Toggle>
</template>