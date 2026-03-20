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
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    class: '',
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" :class="cn('overflow-x-hidden', $props.class)">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
