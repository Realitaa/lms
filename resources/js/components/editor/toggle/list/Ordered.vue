<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { ListOrdered } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('orderedList')"
    @click="editor.chain().focus().toggleOrderedList().run()">
    <ListOrdered />
  </Toggle>
</template>