<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Plus,
    ChevronDown,
    ChevronUp,
    Pencil,
    Check,
    X,
    Trash2,
    GripVertical,
    Save,
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { VueDraggable } from 'vue-draggable-plus';
import AppLayout from '@/layouts/AppLayout.vue';
import type {
    BreadcrumbItem,
    Course,
    Module,
    Lesson,
    Quiz,
    Question,
    Option,
} from '@/types';
import TitleWithBack from '@/components/TitleWithBack.vue';
import AddModuleDialog from '@/components/modules/AddModuleDialog.vue';
import AddLessonDialog from '@/components/modules/AddLessonDialog.vue';
import AddQuizDialog from '@/components/modules/AddQuizDialog.vue';
import DeleteLessonDialog from '@/components/modules/DeleteLessonDialog.vue';
import DeleteModuleDialog from '@/components/modules/DeleteModuleDialog.vue';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import LessonPreview from '@/components/modules/LessonPreview.vue';

const props = defineProps<{
    course: Course;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kursus',
        href: '/courses',
    },
    {
        title: props.course.title,
        href: `/courses/${props.course.id}`,
    },
    {
        title: 'Manajemen Modul',
        href: '#',
    },
];

// --- Display state ---
const display = ref('control');
const isControl = ref(true);

function displayControl() {
    display.value = 'control';
    currentLessonId.value = null;
    currentQuiz.value = null;
    setTimeout(() => {
        isControl.value = true;
    }, 300);
}

// --- Modules data (reactive copy from props) ---
const modules = computed(() => props.course.modules ?? []);

// --- Watch for props changes to re-sync currentQuiz ---
// This is critical: when Inertia refreshes props after adding/deleting questions,
// the currentQuiz ref still holds stale data. This watcher re-syncs it.
watch(
    modules,
    (newModules) => {
        if (currentQuiz.value) {
            const quizId = currentQuiz.value.id;
            for (const mod of newModules) {
                const found = mod.quizzes.find((q) => q.id === quizId);
                if (found) {
                    currentQuiz.value = found;
                    // Clamp question index if questions were deleted
                    if (currentQuestionIndex.value >= found.questions.length) {
                        currentQuestionIndex.value = Math.max(
                            0,
                            found.questions.length - 1,
                        );
                    }
                    return;
                }
            }
            // Quiz was deleted
            currentQuiz.value = null;
            displayControl();
        }

        // Also re-sync currentLessonId for lesson editor
        if (currentLessonId.value) {
            const lesson = findLessonById(currentLessonId.value);
            if (!lesson) {
                currentLessonId.value = null;
                displayControl();
            }
        }
    },
    { deep: true },
);

// --- Collapsed state per module ---
const collapsedModules = ref<Record<number, boolean>>({});

function toggleCollapse(moduleId: number) {
    collapsedModules.value[moduleId] = !collapsedModules.value[moduleId];
}

// --- Add Module Dialog ---
const addModuleDialogOpen = ref(false);

// --- Add Lesson Dialog ---
const addLessonDialogOpen = ref(false);
const addLessonModuleId = ref<number | null>(null);

function openAddLessonDialog(moduleId: number) {
    addLessonModuleId.value = moduleId;
    addLessonDialogOpen.value = true;
}

// --- Add Quiz Dialog ---
const addQuizDialogOpen = ref(false);
const addQuizModuleId = ref<number | null>(null);

function openAddQuizDialog(moduleId: number) {
    addQuizModuleId.value = moduleId;
    addQuizDialogOpen.value = true;
}

// --- Delete Lesson Dialog ---
const deleteLessonDialogOpen = ref(false);
const selectedLesson = ref<Lesson | null>(null);

function openDeleteLessonDialog(lesson: Lesson) {
    selectedLesson.value = lesson;
    deleteLessonDialogOpen.value = true;
}

// --- Delete Module Dialog ---
const deleteModuleDialogOpen = ref(false);
const selectedModule = ref<Module | null>(null);

function openDeleteModuleDialog(mod: Module) {
    selectedModule.value = mod;
    deleteModuleDialogOpen.value = true;
}

// --- Inline edit module name ---
const editingModuleId = ref<number | null>(null);
const editingModuleTitle = ref('');

function startEditModule(mod: Module) {
    editingModuleId.value = mod.id;
    editingModuleTitle.value = mod.title;
}

function cancelEditModule() {
    editingModuleId.value = null;
    editingModuleTitle.value = '';
}

function saveEditModule(mod: Module) {
    if (!editingModuleTitle.value.trim()) return;

    router.put(
        `/courses/${props.course.id}/modules/${mod.id}`,
        {
            title: editingModuleTitle.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Nama modul berhasil diperbarui');
                cancelEditModule();
            },
            onError: (errors) => {
                toast.error(
                    `Gagal memperbarui modul: ${Object.values(errors)[0]}`,
                );
            },
        },
    );
}

// --- Inline edit lesson name ---
const editingLessonId = ref<number | null>(null);
const editingLessonTitle = ref('');

function startEditLesson(lesson: Lesson) {
    editingLessonId.value = lesson.id;
    editingLessonTitle.value = lesson.title;
}

function cancelEditLesson() {
    editingLessonId.value = null;
    editingLessonTitle.value = '';
}

function saveEditLesson(lesson: Lesson) {
    if (!editingLessonTitle.value.trim()) return;

    router.put(
        `/courses/lessons/${lesson.id}`,
        {
            title: editingLessonTitle.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Nama materi berhasil diperbarui');
                cancelEditLesson();
            },
            onError: (errors) => {
                toast.error(
                    `Gagal memperbarui materi: ${Object.values(errors)[0]}`,
                );
            },
        },
    );
}

// --- Drag & Drop: Reorder modules ---
function onModuleDragEnd() {
    const reordered = modules.value.map((mod, idx) => ({
        id: mod.id,
        order: idx,
    }));

    router.post(
        `/courses/${props.course.id}/modules/reorder`,
        {
            modules: reordered,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Urutan modul berhasil diperbarui');
            },
            onError: () => {
                toast.error('Gagal memperbarui urutan modul');
            },
        },
    );
}

// --- Drag & Drop: Reorder lessons within module ---
function onLessonDragEnd(mod: Module) {
    const reordered = mod.lessons.map((lesson, idx) => ({
        id: lesson.id,
        order: idx,
        module_id: mod.id,
    }));

    router.post(
        `/courses/${props.course.id}/lessons/reorder`,
        {
            lessons: reordered,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Urutan materi berhasil diperbarui');
            },
            onError: () => {
                toast.error('Gagal memperbarui urutan materi');
            },
        },
    );
}

// =============================================
// LESSON EDITOR
// =============================================
const currentLessonId = ref<number | null>(null);
const lessonEditorContent = ref<Record<string, unknown>>({});
const lessonHtmlContent = ref('');
const contentEdited = ref(false);
const isSavingLesson = ref(false);

// Find lesson from modules by id
function findLessonById(lessonId: number): Lesson | undefined {
    for (const mod of modules.value) {
        const found = mod.lessons.find((l) => l.id === lessonId);
        if (found) return found;
    }
    return undefined;
}

// Find the module that contains a lesson
function findModuleForLesson(lessonId: number): Module | undefined {
    return modules.value.find((mod) =>
        mod.lessons.some((l) => l.id === lessonId),
    );
}

function displayModule(lessonId: number) {
    const lesson = findLessonById(lessonId);
    if (!lesson) return;

    currentLessonId.value = lessonId;
    currentQuiz.value = null;
    // Load existing content (TipTap JSON) or empty
    lessonEditorContent.value =
        lesson.content && Object.keys(lesson.content).length > 0
            ? { ...lesson.content }
            : {};
    contentEdited.value = false;

    isControl.value = false;
    document.body.style.cursor = 'wait';
    setTimeout(() => {
        display.value = 'module';
        document.body.style.cursor = 'default';
    }, 300);
}

function onLessonEditorUpdate(json: Record<string, unknown>) {
    lessonEditorContent.value = json;
    contentEdited.value = true;
}

function onLessonHtmlUpdate(html: string) {
    lessonHtmlContent.value = html;
}

function saveLessonContent() {
    if (!currentLessonId.value) return;

    // Extract temp image paths from the TipTap JSON content
    const tempImages = extractTempImagePaths(lessonEditorContent.value);

    isSavingLesson.value = true;
    router.put(
        `/courses/lessons/${currentLessonId.value}`,
        {
            content: lessonEditorContent.value as any,
            temp_images: tempImages,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Konten materi berhasil disimpan');
                contentEdited.value = false;
            },
            onError: (errors) => {
                toast.error(
                    `Gagal menyimpan konten: ${Object.values(errors)[0]}`,
                );
            },
            onFinish: () => {
                isSavingLesson.value = false;
            },
        },
    );
}

/**
 * Recursively scan TipTap JSON for image nodes with temp upload URLs.
 * Returns an array of relative storage paths (e.g., 'uploads/tmp/uuid_filename.jpg').
 */
function extractTempImagePaths(json: Record<string, unknown>): string[] {
    const paths: string[] = [];

    function walk(node: Record<string, unknown>) {
        if (
            node.type === 'image' &&
            typeof node.attrs === 'object' &&
            node.attrs !== null
        ) {
            const attrs = node.attrs as Record<string, unknown>;
            const src = typeof attrs.src === 'string' ? attrs.src : '';
            if (src.includes('/uploads/tmp/')) {
                // Extract the relative path: everything after '/storage/'
                const match = src.match(/\/storage\/(.+)$/);
                if (match) {
                    paths.push(match[1]);
                }
            }
        }

        if (Array.isArray(node.content)) {
            for (const child of node.content) {
                walk(child as Record<string, unknown>);
            }
        }
    }

    if (json && typeof json === 'object') {
        walk(json);
    }

    return paths;
}

function cancelLessonEdit() {
    contentEdited.value = false;
    displayControl();
}

// Current lesson title for display
const currentLessonTitle = computed(() => {
    if (!currentLessonId.value) return '';
    const lesson = findLessonById(currentLessonId.value);
    return lesson?.title ?? '';
});

// =============================================
// QUIZ EDITOR
// =============================================
const currentQuiz = ref<Quiz | null>(null);
const currentQuestionIndex = ref(0);

// Editing state for questions
const editingQuestionId = ref<number | null>(null);
const questionEditorContent = ref<Record<string, unknown>>({});
const isSavingQuestion = ref(false);

// Editing state for options
const editingOptionId = ref<number | null>(null);
const optionEditorContent = ref<Record<string, unknown>>({});

function displayQuiz(quiz: Quiz) {
    currentQuiz.value = quiz;
    currentLessonId.value = null;
    currentQuestionIndex.value = 0;
    editingQuestionId.value = null;
    editingOptionId.value = null;

    isControl.value = false;
    document.body.style.cursor = 'wait';
    setTimeout(() => {
        display.value = 'quiz';
        document.body.style.cursor = 'default';
    }, 300);
}

// Current question based on index
const currentQuestion = computed<Question | null>(() => {
    if (!currentQuiz.value || !currentQuiz.value.questions.length) return null;
    return currentQuiz.value.questions[currentQuestionIndex.value] ?? null;
});

function selectQuestion(index: number) {
    currentQuestionIndex.value = index;
    editingQuestionId.value = null;
    editingOptionId.value = null;
}

// --- Question CRUD ---
function addQuestion() {
    if (!currentQuiz.value) return;
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
        `/courses/quizzes/${currentQuiz.value.id}/questions`,
        {
            question_text: emptyContent as any,
            points: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Soal berhasil ditambahkan');
            },
            onError: (errors) => {
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
            question_text: questionEditorContent.value as any,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Soal berhasil diperbarui');
                editingQuestionId.value = null;
            },
            onError: (errors) => {
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
        onError: (errors) => {
            toast.error(`Gagal menghapus soal: ${Object.values(errors)[0]}`);
        },
    });
}

// --- Option CRUD ---
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
            option_text: emptyContent as any,
            is_correct: false,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opsi berhasil ditambahkan');
            },
            onError: (errors) => {
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
            option_text: optionEditorContent.value as any,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opsi berhasil diperbarui');
                editingOptionId.value = null;
            },
            onError: (errors) => {
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
        onError: (errors) => {
            toast.error(`Gagal menghapus opsi: ${Object.values(errors)[0]}`);
        },
    });
}

function deleteQuiz(quiz: Quiz) {
    router.delete(`/courses/quizzes/${quiz.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Kuis berhasil dihapus');
            displayControl();
        },
        onError: (errors) => {
            toast.error(`Gagal menghapus kuis: ${Object.values(errors)[0]}`);
        },
    });
}

// --- Title & subtitle for user activity ---
const titleUserActivity = computed(() => {
    if (display.value === 'module' && currentLessonTitle.value) {
        return `: Mengedit Materi "${currentLessonTitle.value}"`;
    }
    if (display.value === 'quiz' && currentQuiz.value) {
        return `: Mengedit Kuis "${currentQuiz.value.title}"`;
    }
    return '';
});

const subtitleUserActivity = computed(() => {
    if (display.value === 'module' && currentLessonTitle.value) {
        return `Anda sedang mengedit materi "${currentLessonTitle.value}". Jangan lupa untuk menyimpan perubahan.`;
    }
    if (display.value === 'quiz' && currentQuiz.value) {
        return `Anda sedang mengedit kuis "${currentQuiz.value.title}". Jangan lupa untuk menyimpan perubahan.`;
    }
    return `Kelola modul dan materi pada kursus ${props.course.title}`;
});

// Helper: render TipTap JSON to simple text for display in radio labels
function tiptapToText(json: Record<string, unknown>): string {
    if (!json || !json.content) return '';
    try {
        const content = json.content as Array<{
            content?: Array<{ text?: string }>;
        }>;
        return content
            .map((node) =>
                (node.content ?? []).map((c) => c.text ?? '').join(''),
            )
            .join(' ');
    } catch {
        return '';
    }
}

// Helper: split quizzes by type (pre/post)
function preQuizzes(mod: Module): Quiz[] {
    return (mod.quizzes ?? []).filter((q) => q.type === 'pre');
}

function postQuizzes(mod: Module): Quiz[] {
    return (mod.quizzes ?? []).filter((q) => q.type === 'post');
}
</script>

<template>
    <Head :title="`Manajemen Kursus ${course.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <TitleWithBack
                back-url="/courses"
                :title="`Manajemen Kursus ${course.title}${titleUserActivity}`"
                :subtitle="subtitleUserActivity"
            />
        </div>

        <div class="mx-4 flex h-[calc(100vh-200px)] justify-end gap-4">
            <!-- Module content view (70% width) -->
            <div class="w-[70%]" v-if="display == 'module'">
                <Tabs default-value="editor">
                    <div class="flex justify-between">
                        <TabsList>
                            <TabsTrigger value="editor"> Editor </TabsTrigger>
                            <TabsTrigger value="preview"> Preview </TabsTrigger>
                        </TabsList>
                        <!-- show only when content on RichTextEditor is edited -->
                        <div class="flex gap-2">
                            <!-- cancel edit module -->
                            <Button variant="outline" @click="cancelLessonEdit">
                                <X class="h-4 w-4" />
                                Tutup
                            </Button>
                            <!-- save module when clicked -->
                            <Button
                                @click="saveLessonContent"
                                :disabled="isSavingLesson || !contentEdited"
                                v-if="contentEdited"
                            >
                                <Save class="h-4 w-4" />
                                Simpan
                            </Button>
                        </div>
                    </div>
                    <TabsContent value="editor">
                        <!-- edit content of lesson -->
                        <RichTextEditor
                            :model-value="lessonEditorContent"
                            @update:model-value="onLessonEditorUpdate"
                            @update:html-value="onLessonHtmlUpdate"
                            class="max-h-125 min-h-125"
                        />
                    </TabsContent>
                    <TabsContent value="preview">
                        <LessonPreview :content="lessonHtmlContent" />
                    </TabsContent>
                </Tabs>
            </div>

            <!-- Quiz view (70% width) -->
            <Transition name="fade">
                <div
                    class="w-[70%] transition-opacity duration-100"
                    v-if="display == 'quiz' && currentQuiz"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="text-lg font-bold">
                            {{ currentQuiz.title }}
                        </h3>
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="displayControl"
                            >
                                <X class="h-4 w-4" />
                                Tutup
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deleteQuiz(currentQuiz)"
                            >
                                <Trash2 class="h-4 w-4" />
                                Hapus Kuis
                            </Button>
                        </div>
                    </div>
                    <ScrollArea class="w-full px-2 pb-3">
                        <div class="flex w-max gap-2">
                            <!-- quiz question numbers -->
                            <Button
                                v-for="(q, idx) in currentQuiz.questions"
                                :key="q.id"
                                :variant="
                                    currentQuestionIndex === idx
                                        ? 'default'
                                        : 'outline'
                                "
                                @click="selectQuestion(idx)"
                                >{{ idx + 1 }}</Button
                            >
                            <Button variant="ghost" @click="addQuestion">
                                <Plus />
                                Tambah Soal
                            </Button>
                        </div>
                        <ScrollBar
                            orientation="horizontal"
                            class="mx-2 rounded-2xl bg-white"
                        />
                    </ScrollArea>

                    <!-- Question & Options area -->
                    <div
                        class="mt-2 flex h-[calc(100vh-310px)] flex-col space-y-2"
                        v-if="currentQuestion"
                    >
                        <div
                            class="h-1/2 space-y-2 rounded-2xl border py-2 pl-2"
                        >
                            <ScrollArea class="h-full overflow-auto">
                                <!-- Question text -->
                                <div class="mr-2 flex justify-between">
                                    <p class="flex-1">
                                        {{
                                            tiptapToText(
                                                currentQuestion.question_text,
                                            )
                                        }}
                                    </p>
                                    <div class="flex shrink-0 gap-1">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="
                                                startEditQuestion(
                                                    currentQuestion,
                                                )
                                            "
                                            v-if="
                                                editingQuestionId !==
                                                currentQuestion.id
                                            "
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <template
                                            v-if="
                                                editingQuestionId ===
                                                currentQuestion.id
                                            "
                                        >
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="
                                                    saveQuestion(
                                                        currentQuestion,
                                                    )
                                                "
                                            >
                                                <Check class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="cancelEditQuestion"
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </template>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="
                                                deleteQuestion(currentQuestion)
                                            "
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </div>
                                </div>
                                <!-- options -->
                                <RadioGroup class="mt-2">
                                    <div
                                        class="flex items-center space-x-2"
                                        v-for="option in currentQuestion.options"
                                        :key="option.id"
                                    >
                                        <RadioGroupItem
                                            :id="`opt-${option.id}`"
                                            :value="option.id.toString()"
                                            :checked="option.is_correct"
                                            @click="toggleCorrectOption(option)"
                                        />
                                        <Label
                                            :for="`opt-${option.id}`"
                                            :class="
                                                option.is_correct
                                                    ? 'font-bold text-green-600 dark:text-green-400'
                                                    : ''
                                            "
                                        >
                                            {{
                                                tiptapToText(option.option_text)
                                            }}
                                        </Label>
                                        <!-- edit option -->
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="startEditOption(option)"
                                            v-if="editingOptionId !== option.id"
                                        >
                                            <Pencil class="h-3.5 w-3.5" />
                                        </Button>
                                        <template
                                            v-if="editingOptionId === option.id"
                                        >
                                            <!-- save edited option -->
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="saveOption(option)"
                                            >
                                                <Check class="h-3.5 w-3.5" />
                                            </Button>
                                            <!-- cancel editing option -->
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="cancelEditOption"
                                            >
                                                <X class="h-3.5 w-3.5" />
                                            </Button>
                                        </template>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="deleteOption(option)"
                                        >
                                            <Trash2
                                                class="h-3 w-3 text-destructive"
                                            />
                                        </Button>
                                    </div>
                                </RadioGroup>
                                <!-- add new option -->
                                <Button
                                    variant="link"
                                    class="mt-2 p-0!"
                                    @click="addOption"
                                >
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
                                <RichTextEditor
                                    class="max-h-[18.5vh] min-h-25"
                                    :config="[
                                        ['undoRedo'],
                                        [
                                            'bold',
                                            'italic',
                                            'underline',
                                            'strike',
                                        ],
                                    ]"
                                    :model-value="questionEditorContent"
                                    @update:model-value="
                                        (v: Record<string, unknown>) =>
                                            (questionEditorContent = v)
                                    "
                                />
                            </template>
                            <template v-else-if="editingOptionId">
                                <p class="mb-1 text-xs text-muted-foreground">
                                    Mengedit opsi:
                                </p>
                                <RichTextEditor
                                    class="max-h-[18.5vh] min-h-25"
                                    :config="[
                                        ['undoRedo'],
                                        [
                                            'bold',
                                            'italic',
                                            'underline',
                                            'strike',
                                        ],
                                    ]"
                                    :model-value="optionEditorContent"
                                    @update:model-value="
                                        (v: Record<string, unknown>) =>
                                            (optionEditorContent = v)
                                    "
                                />
                            </template>
                            <template v-else>
                                <div
                                    class="flex h-full items-center justify-center rounded-xl border text-sm text-muted-foreground"
                                >
                                    Klik tombol pensil pada soal atau opsi untuk
                                    mengedit
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Empty state for quiz with no questions -->
                    <div
                        v-else
                        class="mt-2 flex flex-col items-center justify-center gap-3 rounded-xl border py-12 text-center text-muted-foreground"
                    >
                        <p class="text-sm">Belum ada soal di kuis ini.</p>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="addQuestion"
                        >
                            <Plus class="mr-1 h-3.5 w-3.5" />
                            Tambah Soal Pertama
                        </Button>
                    </div>
                </div>
            </Transition>

            <!-- Module & Lesson Control Card -->
            <Card
                class="flex h-full flex-col p-4 transition-all duration-500"
                :class="isControl ? 'w-full' : 'w-2/7'"
            >
                <CardHeader class="px-0">
                    <CardTitle>Modul dan Materi</CardTitle>
                    <CardDescription>
                        <span v-if="isControl"
                            >Drag & Drop untuk reposisi materi atau modul. Entri
                            putih adalah materi,
                            <span class="text-blue-700 dark:text-blue-400"
                                >entri biru</span
                            >
                            adalah pre-test,
                            <span class="text-orange-700 dark:text-orange-400"
                                >entri oranye</span
                            >
                            adalah post-test.</span
                        >
                        <span v-else
                            >Klik tombol pensil untuk mengatur modul</span
                        >
                    </CardDescription>
                    <CardAction>
                        <Button
                            variant="ghost"
                            v-if="isControl"
                            @click="addModuleDialogOpen = true"
                        >
                            <Plus />
                            Tambah Modul
                        </Button>
                        <Button
                            size="icon"
                            variant="ghost"
                            v-else
                            @click="displayControl"
                        >
                            <Pencil />
                        </Button>
                    </CardAction>
                </CardHeader>
                <CardContent class="flex-1 overflow-hidden px-0">
                    <ScrollArea class="h-full pr-3">
                        <!-- Draggable module list (control mode) -->
                        <VueDraggable
                            v-if="isControl"
                            v-model="course.modules!"
                            :animation="200"
                            handle=".module-drag-handle"
                            group="modules"
                            @end="onModuleDragEnd"
                            class="space-y-2"
                        >
                            <Card
                                v-for="mod in modules"
                                :key="mod.id"
                                class="w-full gap-2! p-4"
                            >
                                <CardHeader class="border-b px-0 pb-2!">
                                    <!-- Module title: inline editable -->
                                    <div class="flex flex-1 items-center gap-2">
                                        <GripVertical
                                            class="module-drag-handle h-4 w-4 shrink-0 cursor-grab text-muted-foreground"
                                        />

                                        <template
                                            v-if="editingModuleId === mod.id"
                                        >
                                            <Input
                                                v-model="editingModuleTitle"
                                                class="h-8 text-sm"
                                                @keyup.enter="
                                                    saveEditModule(mod)
                                                "
                                                @keyup.escape="cancelEditModule"
                                                autofocus
                                            />
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="shrink-0"
                                                @click="saveEditModule(mod)"
                                            >
                                                <Check class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="shrink-0"
                                                @click="cancelEditModule"
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </template>

                                        <template v-else>
                                            <CardTitle class="text-sm">{{
                                                mod.title
                                            }}</CardTitle>
                                        </template>
                                    </div>

                                    <CardAction class="flex items-center gap-1">
                                        <template
                                            v-if="editingModuleId !== mod.id"
                                        >
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="startEditModule(mod)"
                                            >
                                                <Pencil class="h-3.5 w-3.5" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="
                                                    openDeleteModuleDialog(mod)
                                                "
                                            >
                                                <Trash2
                                                    class="h-3.5 w-3.5 text-destructive"
                                                />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="toggleCollapse(mod.id)"
                                            >
                                                <ChevronDown
                                                    v-if="
                                                        !collapsedModules[
                                                            mod.id
                                                        ]
                                                    "
                                                    class="h-4 w-4"
                                                />
                                                <ChevronUp
                                                    v-else
                                                    class="h-4 w-4"
                                                />
                                            </Button>
                                        </template>
                                    </CardAction>
                                </CardHeader>

                                <CardContent
                                    class="px-0"
                                    v-show="!collapsedModules[mod.id]"
                                >
                                    <!-- Pre-test quizzes (on top, blue) -->
                                    <div
                                        v-for="quiz in preQuizzes(mod)"
                                        :key="`quiz-pre-${quiz.id}`"
                                        class="flex items-center justify-between gap-2 rounded border-b bg-blue-50 px-2 py-2 dark:bg-blue-950/30"
                                    >
                                        <p
                                            class="flex-1 text-sm font-bold text-blue-700 hover:cursor-pointer hover:underline dark:text-blue-400"
                                            @click="displayQuiz(quiz)"
                                        >
                                            {{ quiz.title }}
                                        </p>
                                        <div
                                            class="flex shrink-0 items-center gap-0.5"
                                        >
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-7 w-7"
                                                @click="deleteQuiz(quiz)"
                                            >
                                                <Trash2
                                                    class="h-3 w-3 text-destructive"
                                                />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Draggable lesson list -->
                                    <VueDraggable
                                        v-model="mod.lessons"
                                        :animation="200"
                                        handle=".lesson-drag-handle"
                                        :group="{ name: 'lessons' }"
                                        @end="onLessonDragEnd(mod)"
                                        class="min-h-[2rem]"
                                    >
                                        <div
                                            v-for="lesson in mod.lessons"
                                            :key="lesson.id"
                                            class="flex items-center justify-between gap-2 border-b py-2 pr-2"
                                        >
                                            <GripVertical
                                                class="lesson-drag-handle h-3.5 w-3.5 shrink-0 cursor-grab text-muted-foreground"
                                            />

                                            <!-- Inline edit lesson -->
                                            <template
                                                v-if="
                                                    editingLessonId ===
                                                    lesson.id
                                                "
                                            >
                                                <Input
                                                    v-model="editingLessonTitle"
                                                    class="h-7 flex-1 text-sm"
                                                    @keyup.enter="
                                                        saveEditLesson(lesson)
                                                    "
                                                    @keyup.escape="
                                                        cancelEditLesson
                                                    "
                                                    autofocus
                                                />
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    class="h-7 w-7 shrink-0"
                                                    @click="
                                                        saveEditLesson(lesson)
                                                    "
                                                >
                                                    <Check
                                                        class="h-3.5 w-3.5"
                                                    />
                                                </Button>
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    class="h-7 w-7 shrink-0"
                                                    @click="cancelEditLesson"
                                                >
                                                    <X class="h-3.5 w-3.5" />
                                                </Button>
                                            </template>

                                            <template v-else>
                                                <p
                                                    class="flex-1 text-sm font-bold hover:cursor-pointer hover:underline"
                                                    @click="
                                                        displayModule(lesson.id)
                                                    "
                                                >
                                                    {{ lesson.title }}
                                                </p>
                                                <div
                                                    class="flex shrink-0 items-center gap-0.5"
                                                >
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        class="h-7 w-7"
                                                        @click="
                                                            startEditLesson(
                                                                lesson,
                                                            )
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-3 w-3"
                                                        />
                                                    </Button>
                                                    <Button
                                                        size="icon"
                                                        variant="ghost"
                                                        class="h-7 w-7"
                                                        @click="
                                                            openDeleteLessonDialog(
                                                                lesson,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="h-3 w-3 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </template>
                                        </div>
                                    </VueDraggable>

                                    <!-- Post-test quizzes (on bottom, orange) -->
                                    <div
                                        v-for="quiz in postQuizzes(mod)"
                                        :key="`quiz-post-${quiz.id}`"
                                        class="flex items-center justify-between gap-2 rounded border-b bg-orange-50 px-2 py-2 dark:bg-orange-950/30"
                                    >
                                        <p
                                            class="flex-1 text-sm font-bold text-orange-700 hover:cursor-pointer hover:underline dark:text-orange-400"
                                            @click="displayQuiz(quiz)"
                                        >
                                            {{ quiz.title }}
                                        </p>
                                        <div
                                            class="flex shrink-0 items-center gap-0.5"
                                        >
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-7 w-7"
                                                @click="deleteQuiz(quiz)"
                                            >
                                                <Trash2
                                                    class="h-3 w-3 text-destructive"
                                                />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Add lesson / quiz buttons -->
                                    <div class="flex gap-4">
                                        <Button
                                            variant="link"
                                            class="mt-2 p-0! text-xs"
                                            @click="openAddLessonDialog(mod.id)"
                                        >
                                            <Plus class="h-3.5 w-3.5" />
                                            Tambah Materi
                                        </Button>
                                        <Button
                                            variant="link"
                                            class="mt-2 p-0! text-xs"
                                            @click="openAddQuizDialog(mod.id)"
                                        >
                                            <Plus class="h-3.5 w-3.5" />
                                            Tambah Quiz
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </VueDraggable>

                        <!-- Read-only module list (non-control mode) -->
                        <template v-else>
                            <template v-for="mod in modules" :key="mod.id">
                                <Card class="mb-2 w-full gap-2! p-4">
                                    <CardHeader class="border-b px-0 pb-2!">
                                        <CardTitle>{{ mod.title }}</CardTitle>
                                        <CardAction>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="toggleCollapse(mod.id)"
                                            >
                                                <ChevronDown
                                                    v-if="
                                                        !collapsedModules[
                                                            mod.id
                                                        ]
                                                    "
                                                />
                                                <ChevronUp v-else />
                                            </Button>
                                        </CardAction>
                                    </CardHeader>
                                    <CardContent
                                        class="px-0"
                                        v-show="!collapsedModules[mod.id]"
                                    >
                                        <div
                                            v-for="lesson in mod.lessons"
                                            :key="lesson.id"
                                            class="flex justify-between border-b py-2"
                                        >
                                            <p
                                                class="text-sm font-bold hover:cursor-pointer hover:underline"
                                                @click="
                                                    displayModule(lesson.id)
                                                "
                                                :class="
                                                    currentLessonId ===
                                                    lesson.id
                                                        ? 'text-primary'
                                                        : ''
                                                "
                                            >
                                                {{ lesson.title }}
                                            </p>
                                        </div>
                                        <!-- Pre-test quizzes (read-only) -->
                                        <div
                                            v-for="quiz in preQuizzes(mod)"
                                            :key="`quiz-pre-${quiz.id}`"
                                            class="flex justify-between border-b py-2"
                                        >
                                            <p
                                                class="text-sm font-bold text-blue-700 hover:cursor-pointer hover:underline dark:text-blue-400"
                                                @click="displayQuiz(quiz)"
                                                :class="
                                                    currentQuiz?.id === quiz.id
                                                        ? 'underline'
                                                        : ''
                                                "
                                            >
                                                {{ quiz.title }}
                                            </p>
                                        </div>
                                        <!-- Post-test quizzes (read-only) -->
                                        <div
                                            v-for="quiz in postQuizzes(mod)"
                                            :key="`quiz-post-${quiz.id}`"
                                            class="flex justify-between border-b py-2"
                                        >
                                            <p
                                                class="text-sm font-bold text-orange-700 hover:cursor-pointer hover:underline dark:text-orange-400"
                                                @click="displayQuiz(quiz)"
                                                :class="
                                                    currentQuiz?.id === quiz.id
                                                        ? 'underline'
                                                        : ''
                                                "
                                            >
                                                {{ quiz.title }}
                                            </p>
                                        </div>
                                    </CardContent>
                                </Card>
                            </template>
                        </template>

                        <!-- Empty state -->
                        <div
                            v-if="modules.length === 0"
                            class="flex flex-col items-center justify-center gap-3 py-12 text-center text-muted-foreground"
                        >
                            <p class="text-sm">Belum ada modul.</p>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="addModuleDialogOpen = true"
                                v-if="isControl"
                            >
                                <Plus class="mr-1 h-3.5 w-3.5" />
                                Tambah Modul Pertama
                            </Button>
                        </div>

                        <ScrollBar orientation="vertical" />
                    </ScrollArea>
                </CardContent>
            </Card>
        </div>

        <!-- Dialogs -->
        <AddModuleDialog
            v-model:open="addModuleDialogOpen"
            :course-id="course.id"
        />
        <AddLessonDialog
            v-model:open="addLessonDialogOpen"
            :module-id="addLessonModuleId"
        />
        <AddQuizDialog
            v-model:open="addQuizDialogOpen"
            :module-id="addQuizModuleId"
        />
        <DeleteLessonDialog
            v-model:open="deleteLessonDialogOpen"
            :lesson="selectedLesson"
        />
        <DeleteModuleDialog
            v-model:open="deleteModuleDialogOpen"
            :module="selectedModule"
            :course-id="course.id"
        />
    </AppLayout>
</template>
