<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { VueDraggable } from 'vue-draggable-plus';
import {
    Plus,
    ChevronDown,
    ChevronUp,
    Pencil,
    Check,
    X,
    Trash2,
    GripVertical,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ScrollArea, ScrollBar } from '@/components/ui/scroll-area';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Course, Module, Lesson, Quiz } from '@/types';

export type QuizablePayload = { type: 'course' | 'module' | 'lesson'; id: number };

const props = defineProps<{
    course: Course;
    isControl: boolean;
    currentLessonId: number | null;
    currentQuiz: Quiz | null;
}>();

const emit = defineEmits<{
    (e: 'toggle-control'): void;
    (e: 'display-module', lessonId: number): void;
    (e: 'display-quiz', quiz: Quiz): void;
    (e: 'open-add-module'): void;
    (e: 'open-add-lesson', moduleId: number): void;
    (e: 'open-add-quiz', payload: QuizablePayload): void;
    (e: 'open-delete-module', module: Module): void;
    (e: 'open-delete-lesson', lesson: Lesson): void;
    (e: 'delete-quiz', quiz: Quiz): void;
}>();

const modules = computed(() => props.course.modules ?? []);

// --- Collapsed state per module ---
const collapsedModules = ref<Record<number, boolean>>({});

function toggleCollapse(moduleId: number) {
    collapsedModules.value[moduleId] = !collapsedModules.value[moduleId];
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
            onError: (errors: any) => {
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
            onError: (errors: any) => {
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

// Helper: split quizzes by type (pre/post)
function preQuizzes(mod: Module): Quiz[] {
    return (mod.quizzes ?? []).filter((q) => q.type === 'pre');
}

function postQuizzes(mod: Module): Quiz[] {
    return (mod.quizzes ?? []).filter((q) => q.type === 'post');
}

// Helper: lesson quizzes
function lessonQuizzes(lesson: Lesson): Quiz[] {
    return lesson.quizzes ?? [];
}

// Helper: course-level quizzes
const coursePreQuizzes = computed(() =>
    (props.course.quizzes ?? []).filter((q) => q.type === 'pre'),
);
const coursePostQuizzes = computed(() =>
    (props.course.quizzes ?? []).filter((q) => q.type === 'post'),
);
const hasCourseQuizzes = computed(() =>
    (props.course.quizzes ?? []).length > 0,
);
</script>

<template>
    <Card
        class="flex h-full flex-col p-4 transition-all duration-500"
        :class="isControl ? 'w-full' : 'w-2/7'"
    >
        <CardHeader class="px-0">
            <CardTitle>Modul dan Materi</CardTitle>
            <CardDescription>
                <span v-if="isControl"
                    >Drag & Drop untuk reposisi materi atau modul. Entri putih
                    adalah materi,
                    <span class="text-blue-700 dark:text-blue-400"
                        >entri biru</span
                    >
                    adalah pre-test,
                    <span class="text-orange-700 dark:text-orange-400"
                        >entri oranye</span
                    >
                    adalah post-test,
                    <span class="text-green-700 dark:text-green-400"
                        >entri hijau</span
                    >
                    adalah kuis materi.</span
                >
                <span v-else>Klik tombol pensil untuk mengatur modul</span>
            </CardDescription>
            <CardAction>
                <Button
                    variant="ghost"
                    v-if="isControl"
                    @click="$emit('open-add-module')"
                >
                    <Plus />
                    Tambah Modul
                </Button>
                <Button
                    size="icon"
                    variant="ghost"
                    v-else
                    @click="$emit('toggle-control')"
                >
                    <Pencil />
                </Button>
            </CardAction>
        </CardHeader>
        <CardContent class="flex-1 overflow-hidden px-0">
            <ScrollArea class="h-full pr-3">
                <!-- =============================== -->
                <!-- COURSE-LEVEL QUIZZES (top)      -->
                <!-- =============================== -->
                <template v-if="isControl">
                    <!-- Course pre-test quizzes -->
                    <div
                        v-for="quiz in coursePreQuizzes"
                        :key="`course-quiz-pre-${quiz.id}`"
                        class="flex items-center justify-between gap-2 rounded border-b bg-blue-50 px-2 py-2 dark:bg-blue-950/30"
                    >
                        <p
                            class="flex-1 text-sm font-bold text-blue-700 hover:cursor-pointer hover:underline dark:text-blue-400"
                            @click="$emit('display-quiz', quiz)"
                        >
                            {{ quiz.title }}
                            <span class="text-xs font-normal opacity-60">(Kursus)</span>
                        </p>
                        <div class="flex shrink-0 items-center gap-0.5">
                            <Button
                                size="icon"
                                variant="ghost"
                                class="h-7 w-7"
                                @click="$emit('delete-quiz', quiz)"
                            >
                                <Trash2
                                    class="h-3 w-3 text-destructive"
                                />
                            </Button>
                        </div>
                    </div>

                    <!-- Course post-test quizzes -->
                    <div
                        v-for="quiz in coursePostQuizzes"
                        :key="`course-quiz-post-${quiz.id}`"
                        class="flex items-center justify-between gap-2 rounded border-b bg-orange-50 px-2 py-2 dark:bg-orange-950/30"
                    >
                        <p
                            class="flex-1 text-sm font-bold text-orange-700 hover:cursor-pointer hover:underline dark:text-orange-400"
                            @click="$emit('display-quiz', quiz)"
                        >
                            {{ quiz.title }}
                            <span class="text-xs font-normal opacity-60">(Kursus)</span>
                        </p>
                        <div class="flex shrink-0 items-center gap-0.5">
                            <Button
                                size="icon"
                                variant="ghost"
                                class="h-7 w-7"
                                @click="$emit('delete-quiz', quiz)"
                            >
                                <Trash2
                                    class="h-3 w-3 text-destructive"
                                />
                            </Button>
                        </div>
                    </div>

                    <!-- Add course-level quiz button -->
                    <div class="mb-2 flex gap-4" v-if="isControl">
                        <Button
                            variant="link"
                            class="p-0! text-xs"
                            @click="$emit('open-add-quiz', { type: 'course', id: course.id })"
                        >
                            <Plus class="h-3.5 w-3.5" />
                            Tambah Quiz Kursus
                        </Button>
                    </div>
                </template>

                <!-- Course-level quizzes in read-only mode -->
                <template v-else>
                    <div
                        v-for="quiz in coursePreQuizzes"
                        :key="`course-quiz-pre-${quiz.id}`"
                        class="flex justify-between border-b py-2"
                    >
                        <p
                            class="text-sm font-bold text-blue-700 hover:cursor-pointer hover:underline dark:text-blue-400"
                            @click="$emit('display-quiz', quiz)"
                            :class="currentQuiz?.id === quiz.id ? 'underline' : ''"
                        >
                            {{ quiz.title }}
                            <span class="text-xs font-normal opacity-60">(Kursus)</span>
                        </p>
                    </div>
                    <div
                        v-for="quiz in coursePostQuizzes"
                        :key="`course-quiz-post-${quiz.id}`"
                        class="flex justify-between border-b py-2"
                    >
                        <p
                            class="text-sm font-bold text-orange-700 hover:cursor-pointer hover:underline dark:text-orange-400"
                            @click="$emit('display-quiz', quiz)"
                            :class="currentQuiz?.id === quiz.id ? 'underline' : ''"
                        >
                            {{ quiz.title }}
                            <span class="text-xs font-normal opacity-60">(Kursus)</span>
                        </p>
                    </div>
                </template>

                <!-- =============================== -->
                <!-- MODULES                          -->
                <!-- =============================== -->

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

                                <template v-if="editingModuleId === mod.id">
                                    <Input
                                        v-model="editingModuleTitle"
                                        class="h-8 text-sm"
                                        @keyup.enter="saveEditModule(mod)"
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
                                <template v-if="editingModuleId !== mod.id">
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
                                            $emit('open-delete-module', mod)
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
                                            v-if="!collapsedModules[mod.id]"
                                            class="h-4 w-4"
                                        />
                                        <ChevronUp v-else class="h-4 w-4" />
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
                                    @click="$emit('display-quiz', quiz)"
                                >
                                    {{ quiz.title }}
                                </p>
                                <div class="flex shrink-0 items-center gap-0.5">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-7 w-7"
                                        @click="$emit('delete-quiz', quiz)"
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
                                    class="border-b py-2 pr-2"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <GripVertical
                                            class="lesson-drag-handle h-3.5 w-3.5 shrink-0 cursor-grab text-muted-foreground"
                                        />

                                        <!-- Inline edit lesson -->
                                        <template
                                            v-if="editingLessonId === lesson.id"
                                        >
                                            <Input
                                                v-model="editingLessonTitle"
                                                class="h-7 flex-1 text-sm"
                                                @keyup.enter="
                                                    saveEditLesson(lesson)
                                                "
                                                @keyup.escape="cancelEditLesson"
                                                autofocus
                                            />
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-7 w-7 shrink-0"
                                                @click="saveEditLesson(lesson)"
                                            >
                                                <Check class="h-3.5 w-3.5" />
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
                                                    $emit(
                                                        'display-module',
                                                        lesson.id,
                                                    )
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
                                                    @click="startEditLesson(lesson)"
                                                >
                                                    <Pencil class="h-3 w-3" />
                                                </Button>
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    class="h-7 w-7"
                                                    @click="
                                                        $emit(
                                                            'open-delete-lesson',
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

                                    <!-- Lesson-level quizzes (green) -->
                                    <div
                                        v-for="quiz in lessonQuizzes(lesson)"
                                        :key="`lesson-quiz-${quiz.id}`"
                                        class="ml-5 mt-1 flex items-center justify-between gap-2 rounded bg-green-50 px-2 py-1.5 dark:bg-green-950/30"
                                    >
                                        <p
                                            class="flex-1 text-xs font-bold text-green-700 hover:cursor-pointer hover:underline dark:text-green-400"
                                            @click="$emit('display-quiz', quiz)"
                                        >
                                            {{ quiz.title }}
                                        </p>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-6 w-6"
                                            @click="$emit('delete-quiz', quiz)"
                                        >
                                            <Trash2
                                                class="h-2.5 w-2.5 text-destructive"
                                            />
                                        </Button>
                                    </div>

                                    <!-- Add quiz button for lesson -->
                                    <Button
                                        variant="link"
                                        class="ml-5 mt-1 p-0! text-xs"
                                        @click="$emit('open-add-quiz', { type: 'lesson', id: lesson.id })"
                                    >
                                        <Plus class="h-3 w-3" />
                                        Tambah Quiz Materi
                                    </Button>
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
                                    @click="$emit('display-quiz', quiz)"
                                >
                                    {{ quiz.title }}
                                </p>
                                <div class="flex shrink-0 items-center gap-0.5">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-7 w-7"
                                        @click="$emit('delete-quiz', quiz)"
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
                                    @click="$emit('open-add-lesson', mod.id)"
                                >
                                    <Plus class="h-3.5 w-3.5" />
                                    Tambah Materi
                                </Button>
                                <Button
                                    variant="link"
                                    class="mt-2 p-0! text-xs"
                                    @click="$emit('open-add-quiz', { type: 'module', id: mod.id })"
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
                                            v-if="!collapsedModules[mod.id]"
                                        />
                                        <ChevronUp v-else />
                                    </Button>
                                </CardAction>
                            </CardHeader>
                            <CardContent
                                class="px-0"
                                v-show="!collapsedModules[mod.id]"
                            >
                                <!-- Pre-test quizzes (read-only) -->
                                <div
                                    v-for="quiz in preQuizzes(mod)"
                                    :key="`quiz-pre-${quiz.id}`"
                                    class="flex justify-between border-b py-2"
                                >
                                    <p
                                        class="text-sm font-bold text-blue-700 hover:cursor-pointer hover:underline dark:text-blue-400"
                                        @click="$emit('display-quiz', quiz)"
                                        :class="
                                            currentQuiz?.id === quiz.id
                                                ? 'underline'
                                                : ''
                                        "
                                    >
                                        {{ quiz.title }}
                                    </p>
                                </div>

                                <div
                                    v-for="lesson in mod.lessons"
                                    :key="lesson.id"
                                    class="border-b py-2"
                                >
                                    <div class="flex justify-between">
                                        <p
                                            class="text-sm font-bold hover:cursor-pointer hover:underline"
                                            @click="
                                                $emit('display-module', lesson.id)
                                            "
                                            :class="
                                                currentLessonId === lesson.id
                                                    ? 'text-primary'
                                                    : ''
                                            "
                                        >
                                            {{ lesson.title }}
                                        </p>
                                    </div>

                                    <!-- Lesson-level quizzes (read-only, green) -->
                                    <div
                                        v-for="quiz in lessonQuizzes(lesson)"
                                        :key="`lesson-quiz-${quiz.id}`"
                                        class="ml-4 mt-1 flex justify-between"
                                    >
                                        <p
                                            class="text-xs font-bold text-green-700 hover:cursor-pointer hover:underline dark:text-green-400"
                                            @click="$emit('display-quiz', quiz)"
                                            :class="currentQuiz?.id === quiz.id ? 'underline' : ''"
                                        >
                                            {{ quiz.title }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Post-test quizzes (read-only) -->
                                <div
                                    v-for="quiz in postQuizzes(mod)"
                                    :key="`quiz-post-${quiz.id}`"
                                    class="flex justify-between border-b py-2"
                                >
                                    <p
                                        class="text-sm font-bold text-orange-700 hover:cursor-pointer hover:underline dark:text-orange-400"
                                        @click="$emit('display-quiz', quiz)"
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
                    v-if="modules.length === 0 && !hasCourseQuizzes"
                    class="flex flex-col items-center justify-center gap-3 py-12 text-center text-muted-foreground"
                >
                    <p class="text-sm">Belum ada modul.</p>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="$emit('open-add-module')"
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
</template>
