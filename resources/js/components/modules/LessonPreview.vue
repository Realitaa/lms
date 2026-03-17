<script setup lang="ts">
import { ref, watch, nextTick, onMounted } from 'vue';
import katex from 'katex';

const props = defineProps<{
    content: string;
}>();

const contentEl = ref<HTMLElement | null>(null);

function renderMath() {
    if (!contentEl.value) return;

    const nodes = contentEl.value.querySelectorAll(
        '[data-type="inline-math"], [data-type="block-math"]',
    );

    nodes.forEach((node) => {
        const latex = node.getAttribute('data-latex');
        if (!latex) return;

        const isBlock = node.getAttribute('data-type') === 'block-math';

        node.innerHTML = '';

        katex.render(latex, node as HTMLElement, {
            throwOnError: false,
            displayMode: isBlock,
        });
    });
}

onMounted(async () => {
    await nextTick();
    renderMath();
});

watch(
    () => props.content,
    async () => {
        await nextTick();
        renderMath();
    },
);
</script>

<template>
    <div
        ref="contentEl"
        class="prose max-w-none p-4 dark:prose-invert"
        v-html="content"
    />
</template>
