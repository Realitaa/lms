<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { MessageSquare } from 'lucide-vue-next';
import type { DiscussionThread } from '@/types';

const props = defineProps<{
    thread: DiscussionThread;
}>();

const emit = defineEmits<{
    (e: 'select', thread: DiscussionThread): void;
}>();

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

const userInitials = computed(() => {
    const name = props.thread.user?.name ?? '';
    return name
        .split(' ')
        .map((w) => w[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const contentPreview = computed(() => {
    const text = props.thread.content ?? '';
    return text.length > 180 ? text.slice(0, 180) + '...' : text;
});

const lessonLabel = computed(() => {
    return props.thread.lesson?.title ?? '';
});
</script>

<template>
    <div class="cursor-pointer rounded-xl border border-transparent bg-white px-5 py-4 transition-all duration-200 hover:border-blue-200 hover:shadow-md dark:bg-gray-900 dark:hover:border-blue-800"
        @click="emit('select', thread)">
        <!-- Header: avatar + name + time -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Avatar class="h-9 w-9">
                    <AvatarImage v-if="thread.user?.avatar" :src="thread.user.avatar" :alt="thread.user.name" />
                    <AvatarFallback class="bg-blue-100 text-sm font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                        {{ userInitials }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex items-center gap-2 text-sm">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ thread.user?.name }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ timeAgo(thread.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h3 class="mt-2 text-base font-bold text-gray-900 dark:text-gray-100">
            {{ thread.title }}
        </h3>

        <!-- Content preview -->
        <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            {{ contentPreview }}
        </p>

        <Separator class="my-3" />

        <!-- Footer: reply count + lesson name -->
        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-1.5">
                <MessageSquare class="h-3.5 w-3.5" />
                <span>{{ thread.replies_count ?? 0 }} Pembahasan</span>
            </div>
            <div v-if="lessonLabel" class="flex items-center gap-1.5">
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span>📄 {{ lessonLabel }}</span>
            </div>
        </div>
    </div>
</template>
