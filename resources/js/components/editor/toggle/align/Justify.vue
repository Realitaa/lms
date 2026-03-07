<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { AlignJustify } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive({ textAlign: 'justify' })"
    @click="editor.chain().focus().setTextAlign('justify').run()">
    <AlignJustify />
  </Toggle>
</template>