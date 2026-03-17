```vue lms\resources\js\pages\modules\Index.vue
<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Course, Module, Lesson, Quiz } from '@/types';
import TitleWithBack from '@/components/TitleWithBack.vue';
import AddModuleDialog from '@/components/modules/AddModuleDialog.vue';
import AddLessonDialog from '@/components/modules/AddLessonDialog.vue';
import AddQuizDialog from '@/components/modules/AddQuizDialog.vue';
import type { QuizablePayload } from '@/components/modules/ControlCard.vue';
import DeleteLessonDialog from '@/components/modules/DeleteLessonDialog.vue';
import DeleteModuleDialog from '@/components/modules/DeleteModuleDialog.vue';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import LessonPreview from '@/components/modules/LessonPreview.vue';
import ControlCard from '@/components/modules/ControlCard.vue';
import QuizView from '@/components/modules/QuizView.vue';

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
const currentLessonId = ref<number | null>(null);
const currentQuiz = ref<Quiz | null>(null);

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

// --- Watch for props changes to re-sync currentQuiz and currentLessonId ---
// Helper: find a quiz across all levels (course, module, lesson)
function findQuizById(quizId: number): Quiz | undefined {
    // Search course-level quizzes
    const courseQuizzes = props.course.quizzes ?? [];
    const courseMatch = courseQuizzes.find((q) => q.id === quizId);
    if (courseMatch) return courseMatch;

    // Search module-level and lesson-level quizzes
    for (const mod of modules.value) {
        const modMatch = (mod.quizzes ?? []).find((q) => q.id === quizId);
        if (modMatch) return modMatch;

        for (const lesson of mod.lessons) {
            const lessonMatch = (lesson.quizzes ?? []).find(
                (q) => q.id === quizId,
            );
            if (lessonMatch) return lessonMatch;
        }
    }
    return undefined;
}

watch(
    () => props.course,
    () => {
        if (currentQuiz.value) {
            const foundQuiz = findQuizById(currentQuiz.value.id);
            if (foundQuiz) {
                currentQuiz.value = foundQuiz;
            } else {
                // Quiz was deleted
                currentQuiz.value = null;
                displayControl();
            }
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
const addQuizPayload = ref<QuizablePayload>({ type: 'module', id: 0 });

function openAddQuizDialog(payload: QuizablePayload) {
    addQuizPayload.value = payload;
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

// =============================================
// LESSON EDITOR
// =============================================
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

function displayModule(lessonId: number) {
    const lesson = findLessonById(lessonId);
    if (!lesson) return;
    display.value = 'editor';

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
            onError: (errors: any) => {
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

function displayQuiz(quiz: Quiz) {
    currentQuiz.value = quiz;
    currentLessonId.value = null;

    isControl.value = false;
    document.body.style.cursor = 'wait';
    setTimeout(() => {
        display.value = 'quiz';
        document.body.style.cursor = 'default';
    }, 300);
}

function deleteQuiz(quiz: Quiz) {
    router.delete(`/courses/quizzes/${quiz.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Kuis berhasil dihapus');
            displayControl();
        },
        onError: (errors: any) => {
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
                <QuizView
                    v-if="display == 'quiz' && currentQuiz"
                    :quiz="currentQuiz"
                    @close="displayControl"
                />
            </Transition>

            <!-- Module & Lesson Control Card -->
            <ControlCard
                :course="course"
                :is-control="isControl"
                :current-lesson-id="currentLessonId"
                :current-quiz="currentQuiz"
                @toggle-control="displayControl"
                @display-module="displayModule"
                @display-quiz="displayQuiz"
                @open-add-module="addModuleDialogOpen = true"
                @open-add-lesson="openAddLessonDialog"
                @open-add-quiz="openAddQuizDialog"
                @open-delete-module="openDeleteModuleDialog"
                @open-delete-lesson="openDeleteLessonDialog"
                @delete-quiz="deleteQuiz"
            />
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
            :quizable-type="addQuizPayload.type"
            :quizable-id="addQuizPayload.id"
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
