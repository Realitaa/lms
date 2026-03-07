<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading6 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 6 })"
    @click="editor.chain().focus().toggleHeading({ level: 6 }).run()">
    <Heading6 />
  </Toggle>
</template>