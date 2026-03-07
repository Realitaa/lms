<script setup lang="ts">
  import type { Editor } from '@tiptap/core'
  import { Heading5 } from 'lucide-vue-next'
  import { Toggle } from '@/components/ui/toggle'

  defineProps<{
    editor: Editor
  }>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('heading', { level: 5 })"
    @click="editor.chain().focus().toggleHeading({ level: 5 }).run()">
    <Heading5 />
  </Toggle>
</template>