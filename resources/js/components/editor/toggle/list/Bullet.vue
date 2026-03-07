<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { List } from 'lucide-vue-next'
import { Toggle } from '@/components/ui/toggle'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Toggle size="icon" variant="editor" :model-value="editor.isActive('bulletList')"
    @click="editor.chain().focus().toggleBulletList().run()">
    <List />
  </Toggle>
</template>