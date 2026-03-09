<script setup lang="ts">
import TextAlign from '@tiptap/extension-text-align'
import Underline from '@tiptap/extension-underline'
import StarterKit from '@tiptap/starter-kit'
import Strike from '@tiptap/extension-strike'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import EditorToolbar from './EditorToolbar.vue'
import Image from '@tiptap/extension-image'
import Youtube from '@tiptap/extension-youtube'
import { watch } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  config?: string[][]
  modelValue?: Record<string, unknown>
  htmlValue?: string
  class?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: Record<string, unknown>): void
  (e: 'update:htmlValue', value: string): void
}>()

const editor = useEditor({
  extensions: [
    StarterKit,
    Underline,
    Strike,
    Image,
    Youtube.configure({
      controls: false,
      nocookie: true,
    }),
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
  ],

  content: props.modelValue ?? '<p>Start typing here...</p>',

  editorProps: {
    attributes: {
      class: cn('prose dark:prose-invert max-w-none min-h-[200px] max-h-[300px] my-4 ml-4 pr-4 overflow-y-auto focus:outline-none', props.class),
    },
  },

  onCreate({ editor }) {
    emit('update:modelValue', editor.getJSON())
    emit('update:htmlValue', editor.getHTML())
  },
  onUpdate({ editor }) {
    emit('update:modelValue', editor.getJSON())
    emit('update:htmlValue', editor.getHTML())
  }
})

// Sync external modelValue changes to editor (if parent changes it)
watch(() => props.modelValue, (newVal) => {
  if (!editor.value || !newVal) return
  const currentJson = JSON.stringify(editor.value.getJSON())
  const newJson = JSON.stringify(newVal)
  if (currentJson !== newJson) {
    editor.value.commands.setContent(newVal)
  }
})
</script>

<template>
  <div v-if="editor"
    class="flex flex-col border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 overflow-hidden transition-all duration-200 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">

    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-1">
      <EditorToolbar :editor="editor" :config="config ?? undefined" />
    </div>

    <div class="cursor-text" @click="editor.commands.focus()">
      <EditorContent :editor="editor" />
    </div>

  </div>
</template>

<style>
.tiptap:focus {
  outline: none;
}
</style>