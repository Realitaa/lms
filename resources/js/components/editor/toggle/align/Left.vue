<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { AlignLeft } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive({ textAlign: 'left' })"
    @click="editor.chain().focus().setTextAlign('left').run()">
    <AlignLeft />
  </Toggle>
</template>