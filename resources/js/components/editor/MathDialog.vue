<script setup lang="ts">
import type { Editor } from '@tiptap/core';
import { ref, computed, watch } from 'vue';
import katex from 'katex';

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    open: boolean;
    editor: Editor;
    mode?: 'insert' | 'edit';
    nodePos?: number | null;
    initialLatex?: string;
    initialIsBlock?: boolean;
}>();

const emit = defineEmits(['update:open']);

const latex = ref('');
const isBlock = ref(false);

const preview = computed(() => {
    if (!latex.value) return '';

    try {
        return katex.renderToString(latex.value, {
            throwOnError: false,
            displayMode: isBlock.value,
        });
    } catch {
        return '';
    }
});

function submitMath() {
    if (!latex.value) return;

    props.editor.chain().focus();

    if (props.mode === 'edit' && props.nodePos !== null) {
        props.editor.chain().setNodeSelection(props.nodePos);

        if (isBlock.value) {
            props.editor.commands.updateBlockMath({
                latex: latex.value,
            });
        } else {
            props.editor.commands.updateInlineMath({
                latex: latex.value,
            });
        }
    } else {
        if (isBlock.value) {
            props.editor.commands.insertBlockMath({
                latex: latex.value,
            });
        } else {
            props.editor.commands.insertInlineMath({
                latex: latex.value,
            });
        }
    }

    latex.value = '';
    isBlock.value = false;

    emit('update:open', false);
}

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;

        latex.value = props.initialLatex ?? '';
        isBlock.value = props.initialIsBlock ?? false;
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Menyematkan Rumus</DialogTitle>
                <p class="text-sm text-muted-foreground">
                    Ketik rumus matematika menggunakan format LaTeX.
                </p>
            </DialogHeader>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium"> Formula LaTeX </label>

                    <Input v-model="latex" placeholder="\int_0^1 x^2 dx" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox v-model="isBlock" />
                    <span class="text-sm">
                        Tampilkan sebagai Block (Tengah baris)
                    </span>
                </div>

                <div>
                    <label class="text-sm font-medium"> Pratinjau </label>

                    <div
                        class="flex min-h-16 items-center justify-center rounded-xl border bg-muted/30 p-4"
                        v-html="preview"
                    />
                </div>
            </div>

            <DialogFooter class="gap-2">
                <Button variant="outline" @click="emit('update:open', false)">
                    Batal
                </Button>

                <Button @click="submitMath">
                    {{ mode === 'edit' ? 'Perbarui' : 'Sematkan' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
