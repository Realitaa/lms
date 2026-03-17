<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Search, X, Plus, MessageSquare, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Course, DiscussionThread as ThreadType, DiscussionReply } from '@/types';
import Heading from '@/components/Heading.vue';
import DiscussionThreadCard from '@/components/discussion/DiscussionThread.vue';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
    DialogDescription,
} from '@/components/ui/dialog';

const props = defineProps<{
    courses: Course[];
    threads: ThreadType[];
    filters: {
        search?: string;
        lesson_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Forum Diskusi',
        href: '/discussions',
    },
];

// --- Filters ---
const searchQuery = ref(props.filters.search ?? '');
const selectedLessonId = ref<string>(props.filters.lesson_id ? String(props.filters.lesson_id) : '');

// Build flat lesson list from courses -> modules -> lessons
type LessonOption = {
    id: number;
    title: string;
    courseTitle: string;
    moduleTitle: string;
};

const lessonOptions = computed<LessonOption[]>(() => {
    const options: LessonOption[] = [];
    for (const course of props.courses) {
        for (const mod of course.modules ?? []) {
            for (const lesson of mod.lessons ?? []) {
                options.push({
                    id: lesson.id,
                    title: lesson.title,
                    courseTitle: course.title,
                    moduleTitle: mod.title,
                });
            }
        }
    }
    return options;
});

const selectedLessonLabel = computed(() => {
    if (!selectedLessonId.value) return '';
    const opt = lessonOptions.value.find((l) => l.id === Number(selectedLessonId.value));
    return opt ? `${opt.moduleTitle}: ${opt.title}` : '';
});

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    router.get(
        '/discussions',
        {
            search: searchQuery.value || undefined,
            lesson_id: selectedLessonId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch(selectedLessonId, () => {
    applyFilters();
});

function clearLessonFilter() {
    selectedLessonId.value = '';
}

// --- New Thread Dialog ---
const newThreadOpen = ref(false);
const newThreadLessonId = ref<string>('');
const newThreadTitle = ref('');
const newThreadContent = ref('');
const newThreadHtml = ref('');
const isSubmitting = ref(false);

function openNewThread() {
    newThreadLessonId.value = selectedLessonId.value || '';
    newThreadTitle.value = '';
    newThreadContent.value = '';
    newThreadHtml.value = '';
    newThreadOpen.value = true;
}

function submitThread() {
    if (!newThreadLessonId.value || !newThreadTitle.value || !newThreadContent.value) {
        toast.error('Mohon lengkapi semua field');
        return;
    }

    isSubmitting.value = true;
    router.post(
        '/discussions',
        {
            lesson_id: Number(newThreadLessonId.value),
            title: newThreadTitle.value,
            content: newThreadContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Diskusi berhasil dibuat');
                newThreadOpen.value = false;
            },
            onError: (errors: any) => {
                toast.error(`Gagal membuat diskusi: ${Object.values(errors)[0]}`);
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
}

// --- Thread Detail / Replies ---
const selectedThread = ref<ThreadType | null>(null);
const replyContent = ref('');
const replyHtml = ref('');
const isSubmittingReply = ref(false);

function selectThread(thread: ThreadType) {
    // Reload thread data with replies
    router.get(
        '/discussions',
        {
            search: searchQuery.value || undefined,
            lesson_id: selectedLessonId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                selectedThread.value = thread;
            },
        },
    );
    selectedThread.value = thread;
}

function closeThread() {
    selectedThread.value = null;
}

function submitReply() {
    if (!selectedThread.value || !replyContent.value) {
        toast.error('Mohon isi balasan');
        return;
    }

    isSubmittingReply.value = true;
    router.post(
        `/discussions/${selectedThread.value.id}/replies`,
        {
            content: replyContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Balasan berhasil dikirim');
                replyContent.value = '';
                replyHtml.value = '';
                // Find and update the thread
                const updated = props.threads.find((t) => t.id === selectedThread.value?.id);
                if (updated) {
                    selectedThread.value = updated;
                }
            },
            onError: (errors: any) => {
                toast.error(`Gagal mengirim balasan: ${Object.values(errors)[0]}`);
            },
            onFinish: () => {
                isSubmittingReply.value = false;
            },
        },
    );
}

function deleteThread(thread: ThreadType) {
    router.delete(`/discussions/${thread.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Diskusi berhasil dihapus');
            if (selectedThread.value?.id === thread.id) {
                selectedThread.value = null;
            }
        },
        onError: (errors: any) => {
            toast.error(`Gagal menghapus: ${Object.values(errors)[0]}`);
        },
    });
}

function deleteReply(reply: DiscussionReply) {
    router.delete(`/discussions/replies/${reply.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Balasan berhasil dihapus');
        },
        onError: (errors: any) => {
            toast.error(`Gagal menghapus: ${Object.values(errors)[0]}`);
        },
    });
}

function timeAgo(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit yang lalu`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} jam yang lalu`;

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return `${diffDays} hari yang lalu`;

    const diffWeeks = Math.floor(diffDays / 7);
    if (diffWeeks < 4) return `${diffWeeks} minggu yang lalu`;

    const diffMonths = Math.floor(diffDays / 30);
    if (diffMonths < 12) return `${diffMonths} bulan yang lalu`;

    const diffYears = Math.floor(diffDays / 365);
    return `${diffYears} tahun yang lalu`;
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((w) => w[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

// Editor config for thread content and replies
const editorFeature = [
    ['undoRedo'],
    ['bold', 'italic', 'underline', 'strike'],
    ['bullet', 'ordered'],
];
</script>

<template>
    <Head title="Forum Diskusi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <Heading title="Forum Diskusi" description="Kelola dan pantau diskusi dari setiap materi kursus" />

            <!-- Search + Filter Row -->
            <div class="flex items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <Input v-model="searchQuery" placeholder="Cari judul diskusi" class="pl-10" />
                </div>

                <!-- Lesson Filter Dropdown -->
                <Select v-model="selectedLessonId">
                    <SelectTrigger class="w-[320px]">
                        <SelectValue placeholder="Semua Materi" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">Semua Materi</SelectItem>
                        <template v-for="course in courses" :key="course.id">
                            <template v-for="mod in (course.modules ?? [])" :key="mod.id">
                                <SelectGroup>
                                    <SelectLabel>{{ course.title }} — {{ mod.title }}</SelectLabel>
                                    <SelectItem v-for="lesson in (mod.lessons ?? [])" :key="lesson.id"
                                        :value="String(lesson.id)">
                                        {{ lesson.title }}
                                    </SelectItem>
                                </SelectGroup>
                            </template>
                        </template>
                    </SelectContent>
                </Select>

                <!-- New Thread Button -->
                <Button @click="openNewThread" class="shrink-0">
                    <Plus class="mr-1 h-4 w-4" />
                    Buat Diskusi
                </Button>
            </div>

            <!-- Active Filter Chip -->
            <div v-if="selectedLessonLabel" class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Diskusi berdasarkan:</span>
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-gray-900 px-3 py-1 text-xs font-medium text-white dark:bg-gray-100 dark:text-gray-900">
                    Materi: {{ selectedLessonLabel }}
                    <button @click="clearLessonFilter"
                        class="ml-1 rounded-full p-0.5 transition-colors hover:bg-gray-700 dark:hover:bg-gray-300">
                        <X class="h-3 w-3" />
                    </button>
                </span>
            </div>

            <!-- Thread List + Detail Split -->
            <div class="flex gap-4">
                <!-- Thread List -->
                <div :class="selectedThread ? 'w-[45%]' : 'w-full'" class="transition-all duration-300">
                    <ScrollArea class="h-[calc(100vh-320px)]">
                        <div class="space-y-3 pr-2">
                            <DiscussionThreadCard v-for="thread in threads" :key="thread.id" :thread="thread"
                                @select="selectThread" />

                            <!-- Empty State -->
                            <div v-if="threads.length === 0"
                                class="flex flex-col items-center justify-center gap-3 rounded-xl border py-16 text-center text-muted-foreground">
                                <MessageSquare class="h-10 w-10 text-gray-300" />
                                <p class="text-sm">Belum ada diskusi.</p>
                                <Button variant="outline" size="sm" @click="openNewThread">
                                    <Plus class="mr-1 h-3.5 w-3.5" />
                                    Buat Diskusi Pertama
                                </Button>
                            </div>
                        </div>
                    </ScrollArea>
                </div>

                <!-- Thread Detail Panel -->
                <Transition name="slide">
                    <div v-if="selectedThread"
                        class="w-[55%] rounded-xl border bg-white dark:bg-gray-900">
                        <ScrollArea class="h-[calc(100vh-320px)]">
                            <div class="p-5">
                                <!-- Close + Delete Header -->
                                <div class="mb-4 flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        Detail Diskusi
                                    </h2>
                                    <div class="flex gap-1">
                                        <Button variant="ghost" size="icon" @click="deleteThread(selectedThread)">
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="closeThread">
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>

                                <!-- Thread Author -->
                                <div class="flex items-center gap-3">
                                    <Avatar class="h-10 w-10">
                                        <AvatarImage v-if="selectedThread.user?.avatar"
                                            :src="selectedThread.user.avatar" />
                                        <AvatarFallback
                                            class="bg-blue-100 font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                            {{ getInitials(selectedThread.user?.name ?? '') }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ selectedThread.user?.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ timeAgo(selectedThread.created_at) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Title + Content -->
                                <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ selectedThread.title }}
                                </h3>
                                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                                    {{ selectedThread.content }}
                                </p>

                                <!-- Lesson info -->
                                <div v-if="selectedThread.lesson"
                                    class="mt-3 text-xs text-gray-500">
                                    📄 {{ selectedThread.lesson?.title }}
                                </div>

                                <Separator class="my-5" />

                                <!-- Replies -->
                                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ selectedThread.replies?.length ?? selectedThread.replies_count ?? 0 }} Balasan
                                </h4>

                                <div class="space-y-4">
                                    <div v-for="reply in (selectedThread.replies ?? [])" :key="reply.id"
                                        class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <Avatar class="h-7 w-7">
                                                    <AvatarImage v-if="reply.user?.avatar"
                                                        :src="reply.user.avatar" />
                                                    <AvatarFallback
                                                        class="bg-green-100 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-300">
                                                        {{ getInitials(reply.user?.name ?? '') }}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ reply.user?.name }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ timeAgo(reply.created_at) }}
                                                </span>
                                            </div>
                                            <Button variant="ghost" size="icon" class="h-6 w-6"
                                                @click="deleteReply(reply)">
                                                <Trash2 class="h-3 w-3 text-destructive" />
                                            </Button>
                                        </div>
                                        <p class="mt-1.5 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">
                                            {{ reply.content }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Reply Form -->
                                <div class="mt-5">
                                    <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Tulis Balasan
                                    </p>
                                    <textarea v-model="replyContent" rows="3"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                                        placeholder="Ketik balasan Anda..." />
                                    <div class="mt-2 flex justify-end">
                                        <Button size="sm" @click="submitReply" :disabled="isSubmittingReply || !replyContent">
                                            Kirim Balasan
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </ScrollArea>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- New Thread Dialog -->
        <Dialog v-model:open="newThreadOpen">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Buat Diskusi Baru</DialogTitle>
                    <DialogDescription>
                        Buat diskusi baru untuk materi kursus yang dipilih.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <!-- Lesson Select -->
                    <div>
                        <label class="mb-1 block text-sm font-medium">Materi</label>
                        <Select v-model="newThreadLessonId">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Pilih materi" />
                            </SelectTrigger>
                            <SelectContent>
                                <template v-for="course in courses" :key="course.id">
                                    <template v-for="mod in (course.modules ?? [])" :key="mod.id">
                                        <SelectGroup>
                                            <SelectLabel>{{ course.title }} — {{ mod.title }}</SelectLabel>
                                            <SelectItem v-for="lesson in (mod.lessons ?? [])" :key="lesson.id"
                                                :value="String(lesson.id)">
                                                {{ lesson.title }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </template>
                                </template>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="mb-1 block text-sm font-medium">Judul Diskusi</label>
                        <Input v-model="newThreadTitle" placeholder="Masukkan judul diskusi" />
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="mb-1 block text-sm font-medium">Isi Diskusi</label>
                        <textarea v-model="newThreadContent" rows="5"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            placeholder="Tulis isi diskusi Anda..." />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="newThreadOpen = false">Batal</Button>
                    <Button @click="submitThread" :disabled="isSubmitting">
                        Buat Diskusi
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.slide-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
