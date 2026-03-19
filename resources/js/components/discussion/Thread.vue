<script setup lang="ts">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
import RichTextEditor from '@/components/editor/RichTextEditor.vue';
import ContentRenderer from '@/components/courses/ContentRenderer.vue';
import { tiptapJsonToHtml } from '@/utils/tiptapToHtml';
import { X, MessageSquare, BookOpen } from 'lucide-vue-next';

const props = defineProps<{
    thread: any;
}>();

const emit = defineEmits(['close']);
const page = usePage();

const replyForm = useForm<{ content: any }>({
    content: null,
});

const submitReply = () => {
    replyForm.transform((data) => ({
        ...data,
        content: data.content ? JSON.stringify(data.content) : '',
    })).post(`/discussions/${props.thread.id}/replies`, {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset();
        },
    });
};

const renderContent = (content: string) => {
    if (!content) return '';
    try {
        const json = JSON.parse(content);
        return tiptapJsonToHtml(json);
    } catch {
        return content;
    }
};
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <Avatar class="h-10 w-10">
                    <AvatarImage :src="thread.user?.avatar" />
                    <AvatarFallback>{{ thread.user?.name?.charAt(0) || 'U' }}</AvatarFallback>
                </Avatar>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm">{{ thread.user?.name }}</span>
                        <span class="text-xs text-muted-foreground">• {{ new Date(thread.created_at).toLocaleDateString() }}</span>
                    </div>
                </div>
            </div>
            
            <Button variant="ghost" size="icon" @click="emit('close')">
                <X class="h-4 w-4" />
            </Button>
        </div>

        <!-- Thread Content -->
        <div>
            <h1 class="text-xl font-bold mb-4">{{ thread.title }}</h1>
            <ContentRenderer :content="renderContent(thread.content)" class="mb-4 p-0" />
            
            <Badge variant="secondary" class="mr-2">#error</Badge>
            <!-- Badge "SELESAI" is excluded as requested -->
        </div>

        <!-- Thread Footer Info -->
        <div class="flex items-center gap-6 py-4 border-t border-b text-sm text-muted-foreground">
            <div class="flex items-center gap-2">
                <MessageSquare class="h-4 w-4 text-muted-foreground" />
                <span>{{ thread.replies_count || thread.replies?.length || 0 }} Pembahasan</span>
            </div>
            <div class="flex items-center gap-2">
                <BookOpen class="h-4 w-4 text-muted-foreground" />
                <span>Latihan: {{ thread.lesson?.title }}</span>
            </div>
        </div>

        <!-- Reply Form -->
        <div class="bg-muted/30 rounded-lg p-4 mt-2">
            <div class="flex items-center gap-3 mb-4">
                <Avatar class="h-8 w-8">
                    <AvatarImage :src="(page.props.auth as any)?.user?.avatar" />
                    <AvatarFallback>{{ (page.props.auth as any)?.user?.name?.charAt(0) || 'U' }}</AvatarFallback>
                </Avatar>
                <span class="font-medium text-sm">{{ (page.props.auth as any)?.user?.name }}</span>
            </div>
            
            <form @submit.prevent="submitReply">
                <div class="border rounded-md bg-background mb-3">
                    <RichTextEditor v-model="(replyForm.content as any)" placeholder="Tuliskan komentar Anda..." />
                </div>
                <div class="flex justify-end">
                    <Button type="submit" :disabled="replyForm.processing">Balas</Button>
                </div>
            </form>
        </div>

        <!-- Replies List -->
        <div class="mt-4 flex flex-col gap-4">
            <div v-for="reply in thread.replies" :key="reply.id" class="border rounded-lg p-4">
                <div class="flex items-start gap-3 mb-3">
                    <Avatar class="h-10 w-10">
                        <AvatarImage :src="reply.user?.avatar" />
                        <AvatarFallback>{{ reply.user?.name?.charAt(0) || 'U' }}</AvatarFallback>
                    </Avatar>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm">{{ reply.user?.name }}</span>
                            <!-- Mocking Reviewer badge conditionally based on screenshot, could be dynamic if role exists -->
                            <Badge v-if="reply.user?.role === 'reviewer'" variant="outline" class="text-blue-500 border-blue-200 bg-blue-50 text-[10px] h-5 px-1.5">Reviewer</Badge>
                        </div>
                        <div class="text-xs text-muted-foreground">{{ new Date(reply.created_at).toLocaleDateString() }}</div>
                    </div>
                </div>
                <!-- Cleanly render the HTML content from rich text editor -->
                <ContentRenderer :content="renderContent(reply.content)" class="p-0" />
            </div>
        </div>
    </div>
</template>
