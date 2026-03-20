<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner'
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'vue-sonner/style.css'

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    class?: string;
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    class: '',
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :class="$props.class">
        <slot />
    </AppLayout>
    <Toaster position="top-center" rich-colors />
</template>
