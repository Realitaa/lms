<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem } from '@/types';
import { cn } from '@/lib/utils';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    class?: string;
    sidebar?: boolean;
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    class: '',
    sidebar: true,
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar v-if="sidebar" />
        <AppContent variant="sidebar" :class="cn('overflow-x-hidden', $props.class)">
            <AppSidebarHeader v-if="sidebar" :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
