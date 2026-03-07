<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading1 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 1 })"
    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()">
    <Heading1 />
  </Toggle>
</template>