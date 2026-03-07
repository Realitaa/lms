<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import { Undo2, Redo2 } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

defineProps<{
  editor: Editor
}>()
</script>

<template>
  <Button
    variant="ghost"
    class="h-8 w-8"
    @click="editor.chain().focus().undo().run()"
    :disabled="!editor.can().undo()"
  >
    <Undo2 />
  </Button>

  <Button
    variant="ghost"
    class="h-8 w-8"
    @click="editor.chain().focus().redo().run()"
    :disabled="!editor.can().redo()"
  >
    <Redo2 />
  </Button>
</template>