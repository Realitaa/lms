<script setup lang="ts">
import type { Editor } from '@tiptap/core'
import type { Component } from 'vue'

import AddImage from './button/AddImage.vue'
import AddYoutube from './button/AddYoutube.vue'
import UndoRedo from './button/UndoRedo.vue'
import Math from './button/AddMath.vue'
import { Left, Center, Right, Justify } from './toggle/align'
import { Bold, Italic, Underline, Strike } from './toggle/format'
import { Header1, Header2, Header3, Header4, Header5, Header6 } from './toggle/header'
import { Bullet, Ordered } from './toggle/list'
import ToggleSeparator from './toggle/ToggleSeparator.vue'

withDefaults(defineProps<{
  editor: Editor
  config?: string[][]
}>(), {
  config: () => [
    ['undoRedo'],
    ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
    ['bold', 'italic', 'underline', 'strike'],
    ['bullet', 'ordered'],
    ['left', 'center', 'right', 'justify'],
    ['image', 'youtube', 'math']
  ]
})

const componentMap: Record<string, Component> = {
  undoRedo: UndoRedo,
  h1: Header1, h2: Header2, h3: Header3, h4: Header4, h5: Header5, h6: Header6,
  bold: Bold, italic: Italic, underline: Underline, strike: Strike,
  bullet: Bullet, ordered: Ordered,
  left: Left, center: Center, right: Right, justify: Justify,
  image: AddImage, youtube: AddYoutube, math: Math
}
</script>

<template>
  <div class="flex flex-wrap p-2 gap-y-2">
    <div 
      v-for="(group, groupIndex) in config" 
      :key="groupIndex"
      class="flex items-center h-6"
    >
      <component
        v-for="item in group"
        :key="item"
        :is="componentMap[item]"
        :editor="editor"
      />

      <ToggleSeparator v-if="groupIndex < config.length - 1" class="mx-1" />
    </div>
  </div>
</template>