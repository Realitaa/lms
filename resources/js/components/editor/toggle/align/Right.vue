<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { AlignRight } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive({ textAlign: 'right' })"
    @click="editor.chain().focus().setTextAlign('right').run()">
    <AlignRight />
  </Toggle>
</template>