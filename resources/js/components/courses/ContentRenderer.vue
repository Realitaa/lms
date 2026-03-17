<script setup lang="ts">
import { ref, toRef } from 'vue';
import { useMathRenderer } from '@/composables/useMathRenderer';
import { cn } from '@/lib/utils';

const props = defineProps<{
    content: string;
    class?: string;
}>();

const contentEl = ref<HTMLElement | null>(null);

useMathRenderer(contentEl, toRef(props, 'content'));
</script>

<template>
    <div ref="contentEl" :class="cn('prose max-w-none p-4 dark:prose-invert', props.class)" v-html="content" />
</template>
