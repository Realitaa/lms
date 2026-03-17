<script setup lang="ts">
import TextAlign from '@tiptap/extension-text-align';
import 'katex/dist/katex.min.css';
import Underline from '@tiptap/extension-underline';
import StarterKit from '@tiptap/starter-kit';
import Strike from '@tiptap/extension-strike';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import EditorToolbar from './EditorToolbar.vue';
import MathDialog from './MathDialog.vue';
import Image from '@tiptap/extension-image';
import Youtube from '@tiptap/extension-youtube';
import { Mathematics } from '@tiptap/extension-mathematics';
import { ref, watch } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    config?: string[][];
    modelValue?: Record<string, unknown>;
    htmlValue?: string;
    class?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: Record<string, unknown>): void;
    (e: 'update:htmlValue', value: string): void;
}>();

const openMathDialog = ref(false);
const mathEditState = ref<{
    latex: string;
    pos: number | null;
    isBlock: boolean;
}>({
    latex: '',
    pos: null,
    isBlock: false,
});

const editor = useEditor({
    extensions: [
        StarterKit,
        Underline,
        Strike,
        Image,
        Mathematics.configure({
            inlineOptions: {
                onClick: (node, pos) => {
                    mathEditState.value = {
                        latex: node.attrs.latex,
                        pos,
                        isBlock: false,
                    };

                    openMathDialog.value = true;
                },
            },

            blockOptions: {
                onClick: (node, pos) => {
                    mathEditState.value = {
                        latex: node.attrs.latex,
                        pos,
                        isBlock: true,
                    };

                    openMathDialog.value = true;
                },
            },
        }),
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
            class: cn(
                'my-4 prose ml-4 max-h-[300px] min-h-[200px] max-w-none overflow-y-auto pr-4 focus:outline-none dark:prose-invert',
                props.class,
            ),
        },
    },

    onCreate({ editor }) {
        emit('update:modelValue', editor.getJSON());
        emit('update:htmlValue', editor.getHTML());
    },
    onUpdate({ editor }) {
        emit('update:modelValue', editor.getJSON());
        emit('update:htmlValue', editor.getHTML());
    },
});

// Sync external modelValue changes to editor (if parent changes it)
watch(
    () => props.modelValue,
    (newVal) => {
        if (!editor.value || !newVal) return;
        const currentJson = JSON.stringify(editor.value.getJSON());
        const newJson = JSON.stringify(newVal);
        if (currentJson !== newJson) {
            editor.value.commands.setContent(newVal);
        }
    },
);
</script>

<template>
    <div
        v-if="editor"
        class="flex flex-col overflow-hidden rounded-xl border border-gray-300 bg-white transition-all duration-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500 dark:border-gray-700 dark:bg-gray-900"
    >
        <div
            class="border-b border-gray-200 bg-gray-50 px-3 py-1 dark:border-gray-700 dark:bg-gray-800"
        >
            <EditorToolbar :editor="editor" :config="config ?? undefined" />
        </div>

        <div class="cursor-text" @click="editor.commands.focus()">
            <EditorContent :editor="editor" />
        </div>

        <MathDialog
            v-model:open="openMathDialog"
            :editor="editor"
            mode="edit"
            :nodePos="mathEditState.pos"
            :initialLatex="mathEditState.latex"
            :initialIsBlock="mathEditState.isBlock"
        />
    </div>
</template>
