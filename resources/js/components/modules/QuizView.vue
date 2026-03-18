<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Plus, X, Trash2, Pencil, Check } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import type { Quiz, Question, Option } from '@/types';
import ContentRenderer from '../courses/ContentRenderer.vue';
import { tiptapJsonToHtml } from '@/utils/tiptapToHtml'
import { quizFeatures } from '@/config/editorFeature'

const props = defineProps<{
    quiz: Quiz;
}>();

const emit = defineEmits(['close']);

const currentQuestionIndex = ref(0);
const editingQuestionId = ref<number | null>(null);
const questionEditorContent = ref<Record<string, unknown>>({});
const isSavingQuestion = ref(false);
const editingOptionId = ref<number | null>(null);
const optionEditorContent = ref<Record<string, unknown>>({});

// Computed current question
const currentQuestion = computed<Question | null>(() => {
    if (!props.quiz || !props.quiz.questions || !props.quiz.questions.length)
        return null;
    return props.quiz.questions[currentQuestionIndex.value] ?? null;
});

// Watch quiz changes to keep index within bounds
watch(
    () => props.quiz,
    (newQuiz) => {
        if (newQuiz && newQuiz.questions) {
            if (currentQuestionIndex.value >= newQuiz.questions.length) {
                currentQuestionIndex.value = Math.max(
                    0,
                    newQuiz.questions.length - 1,
                );
            }
        } else {
            // Handle case where quiz might be null (though prop type says Quiz)
            currentQuestionIndex.value = 0;
        }
    },
    { deep: true },
);

function selectQuestion(index: number) {
    currentQuestionIndex.value = index;
    editingQuestionId.value = null;
    editingOptionId.value = null;
}

function tiptapToHtml(json: Record<string, unknown>): string {
    if (!json || !json.content) return '';
    return tiptapJsonToHtml(json);
}

function addQuestion() {
    const emptyContent = {
        type: 'doc',
        content: [
            {
                type: 'paragraph',
                content: [{ type: 'text', text: 'Soal baru' }],
            },
        ],
    };

    router.post(
        `/courses/quizzes/${props.quiz.id}/questions`,
        {
            question_text: emptyContent,
            points: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Soal berhasil ditambahkan');
            },
            onError: (errors: any) => {
                toast.error(
                    `Gagal menambahkan soal: ${Object.values(errors)[0]}`,
                );
            },
        },
    );
}

function startEditQuestion(question: Question) {
    editingQuestionId.value = question.id;
    questionEditorContent.value = { ...question.question_text };
    editingOptionId.value = null;
}

function cancelEditQuestion() {
    editingQuestionId.value = null;
    questionEditorContent.value = {};
}

function saveQuestion(question: Question) {
    isSavingQuestion.value = true;
    router.put(
        `/courses/questions/${question.id}`,
        {
            question_text: questionEditorContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Soal berhasil diperbarui');
                editingQuestionId.value = null;
            },
            onError: (errors: any) => {
                toast.error(
                    `Gagal menyimpan soal: ${Object.values(errors)[0]}`,
                );
            },
            onFinish: () => {
                isSavingQuestion.value = false;
            },
        },
    );
}

function deleteQuestion(question: Question) {
    router.delete(`/courses/questions/${question.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Soal berhasil dihapus');
            if (currentQuestionIndex.value > 0) {
                currentQuestionIndex.value--;
            }
        },
        onError: (errors: any) => {
            toast.error(`Gagal menghapus soal: ${Object.values(errors)[0]}`);
        },
    });
}

function addOption() {
    if (!currentQuestion.value) return;
    const emptyContent = {
        type: 'doc',
        content: [
            {
                type: 'paragraph',
                content: [{ type: 'text', text: 'Opsi baru' }],
            },
        ],
    };

    router.post(
        `/courses/questions/${currentQuestion.value.id}/options`,
        {
            option_text: emptyContent,
            is_correct: false,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opsi berhasil ditambahkan');
            },
            onError: (errors: any) => {
                toast.error(
                    `Gagal menambahkan opsi: ${Object.values(errors)[0]}`,
                );
            },
        },
    );
}

function startEditOption(option: Option) {
    editingOptionId.value = option.id;
    optionEditorContent.value = { ...option.option_text };
    editingQuestionId.value = null;
}

function cancelEditOption() {
    editingOptionId.value = null;
    optionEditorContent.value = {};
}

function saveOption(option: Option) {
    router.put(
        `/courses/options/${option.id}`,
        {
            option_text: optionEditorContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opsi berhasil diperbarui');
                editingOptionId.value = null;
            },
            onError: (errors: any) => {
                toast.error(
                    `Gagal menyimpan opsi: ${Object.values(errors)[0]}`,
                );
            },
        },
    );
}

function toggleCorrectOption(option: Option) {
    router.put(
        `/courses/options/${option.id}`,
        {
            is_correct: !option.is_correct,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(
                    option.is_correct
                        ? 'Opsi ditandai sebagai salah'
                        : 'Opsi ditandai sebagai benar',
                );
            },
        },
    );
}

function deleteOption(option: Option) {
    router.delete(`/courses/options/${option.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Opsi berhasil dihapus');
        },
        onError: (errors: any) => {
            toast.error(`Gagal menghapus opsi: ${Object.values(errors)[0]}`);
        },
    });
}

function deleteQuiz() {
    router.delete(`/courses/quizzes/${props.quiz.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Kuis berhasil dihapus');
            emit('close');
        },
        onError: (errors: any) => {
            toast.error(`Gagal menghapus kuis: ${Object.values(errors)[0]}`);
        },
    });
}
</script>

<template>
    <div class="w-[70%] transition-opacity duration-100">
        <div class="mb-2 flex items-center justify-between">
            <h3 class="text-lg font-bold">
                {{ quiz.title }}
            </h3>
            <div class="flex gap-2">
                <Button variant="outline" size="sm" @click="$emit('close')">
                    <X class="h-4 w-4" />
                    Tutup
                </Button>
                <Button variant="destructive" size="sm" @click="deleteQuiz">
                    <Trash2 class="h-4 w-4" />
                    Hapus Kuis
                </Button>
            </div>
        </div>
        <ScrollArea class="w-full px-2 pb-3">
            <div class="flex w-max gap-2">
                <!-- quiz question numbers -->
                <Button v-for="(q, idx) in quiz.questions" :key="q.id" :variant="currentQuestionIndex === idx ? 'default' : 'outline'
                    " @click="selectQuestion(idx)">{{ idx + 1 }}</Button>
                <Button variant="ghost" @click="addQuestion">
                    <Plus />
                    Tambah Soal
                </Button>
            </div>
            <ScrollBar orientation="horizontal" class="mx-2 rounded-2xl bg-white" />
        </ScrollArea>

        <!-- Question & Options area -->
        <div class="mt-2 flex h-[calc(100vh-310px)] flex-col space-y-2" v-if="currentQuestion">
            <div class="h-1/2 space-y-2 rounded-2xl border py-2 pl-2">
                <ScrollArea class="h-full overflow-auto">
                    <!-- Question text -->
                    <div class="mr-2 flex justify-between">
                        <ContentRenderer :content="tiptapToHtml(currentQuestion.question_text)" class="p-0" />
                        <div class="flex shrink-0 gap-1">
                            <Button size="icon" variant="ghost" @click="startEditQuestion(currentQuestion)"
                                v-if="editingQuestionId !== currentQuestion.id">
                                <Pencil class="h-4 w-4" />
                            </Button>
                            <template v-if="editingQuestionId === currentQuestion.id">
                                <Button size="icon" variant="ghost" @click="saveQuestion(currentQuestion)">
                                    <Check class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="ghost" @click="cancelEditQuestion">
                                    <X class="h-4 w-4" />
                                </Button>
                            </template>
                            <Button size="icon" variant="ghost" @click="deleteQuestion(currentQuestion)">
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                    <!-- options -->
                    <RadioGroup class="mt-2">
                        <div class="flex items-center space-x-2" v-for="option in currentQuestion.options"
                            :key="option.id">
                            <RadioGroupItem :id="`opt-${option.id}`" :value="option.id.toString()"
                                :checked="option.is_correct" @click="toggleCorrectOption(option)" />
                            <Label :for="`opt-${option.id}`" :class="option.is_correct
                                ? 'font-bold text-green-600 dark:text-green-400'
                                : ''
                                ">
                                <ContentRenderer :content="tiptapToHtml(option.option_text)" class="p-0" />
                            </Label>
                            <!-- edit option -->
                            <Button size="icon" variant="ghost" @click="startEditOption(option)"
                                v-if="editingOptionId !== option.id">
                                <Pencil class="h-3.5 w-3.5" />
                            </Button>
                            <template v-if="editingOptionId === option.id">
                                <!-- save edited option -->
                                <Button size="icon" variant="ghost" @click="saveOption(option)">
                                    <Check class="h-3.5 w-3.5" />
                                </Button>
                                <!-- cancel editing option -->
                                <Button size="icon" variant="ghost" @click="cancelEditOption">
                                    <X class="h-3.5 w-3.5" />
                                </Button>
                            </template>
                            <Button size="icon" variant="ghost" @click="deleteOption(option)">
                                <Trash2 class="h-3 w-3 text-destructive" />
                            </Button>
                        </div>
                    </RadioGroup>
                    <!-- add new option -->
                    <Button variant="link" class="mt-2 p-0!" @click="addOption">
                        <Plus />
                        Tambah Opsi
                    </Button>
                </ScrollArea>
            </div>
            <!-- Editor for editing question or option text -->
            <div class="h-1/2">
                <template v-if="editingQuestionId">
                    <p class="mb-1 text-xs text-muted-foreground">
                        Mengedit soal:
                    </p>
                    <RichTextEditor class="max-h-[18.5vh] min-h-25" :config="quizFeatures"
                        :model-value="questionEditorContent" @update:model-value="
                            (v: Record<string, unknown>) =>
                                (questionEditorContent = v)
                        " />
                </template>
                <template v-else-if="editingOptionId">
                    <p class="mb-1 text-xs text-muted-foreground">
                        Mengedit opsi:
                    </p>
                    <RichTextEditor class="max-h-[18.5vh] min-h-25" :config="quizFeatures"
                        :model-value="optionEditorContent" @update:model-value="
                            (v: Record<string, unknown>) =>
                                (optionEditorContent = v)
                        " />
                </template>
                <template v-else>
                    <div
                        class="flex h-full items-center justify-center rounded-xl border text-sm text-muted-foreground">
                        Klik tombol pensil pada soal atau opsi untuk mengedit
                    </div>
                </template>
            </div>
        </div>

        <!-- Empty state for quiz with no questions -->
        <div v-else
            class="mt-2 flex flex-col items-center justify-center gap-3 rounded-xl border py-12 text-center text-muted-foreground">
            <p class="text-sm">Belum ada soal di kuis ini.</p>
            <Button variant="outline" size="sm" @click="addQuestion">
                <Plus class="mr-1 h-3.5 w-3.5" />
                Tambah Soal Pertama
            </Button>
        </div>
    </div>
</template>
